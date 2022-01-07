<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Payjp\Charge;

class ProductsController extends Controller
{
    /**
     * 商品一覧を表示する
     *
     * @return view
     */
    public function showProducts(Request $request){

        $query = Product::query();

        // カテゴリで絞り込み
        if ($request->filled('category')) {
            list($categoryType, $categoryID) = explode(':', $request->input('category'));

            if ($categoryType === 'primary') {
                $query->whereHas('secondaryCategory', function ($query) use ($categoryID) {
                    $query->where('primary_category_id', $categoryID);
                });
            } else if ($categoryType === 'secondary') {
                $query->where('secondary_category_id', $categoryID);
            }
        }

        // キーワードで絞り込み
        if ($request->filled('keyword')) {
            $keyword = '%' . $this->escape($request->input('keyword')) . '%';
            $query->where(function ($query) use ($keyword) {
                $query->where('name', 'LIKE', $keyword);
                $query->orWhere('description', 'LIKE', $keyword);
            });
        }

        $products = $query->orderByRaw("FIELD(state, '" . Product::STATE_SELLING . "', '" . Product::STATE_BOUGHT . "')")
            ->orderBy('id', 'DESC')
            ->paginate(52);

        return view('products.products')
            ->with('products', $products);
    }

    /**
     * キーワードをエスケープする
     */
    private function escape(string $value)
    {
        return str_replace(
            ['\\', '%', '_'],
            ['\\\\', '\\%', '\\_'],
            $value
        );
    }

    /**
     * 商品詳細を表示する
     * @param int $product 商品ID
     * @return view
     */
    public function showProductDetail(Product $product){

        return view('products.product_detail')->with('product', $product);
    }

    /**
     * 商品購入画面を表示する
     * @param  $product
     * @return view
     */
    public function showBuyProductForm(Product $product)
    {
        if (!$product->isStateSelling) {
            abort(404);
        }

        return view('products.product_buy_form')
        ->with('product', $product);
    }

    /**
     * 商品購入する
     * @param  $product
     * @return string
     */
    public function buyProduct(Request $request, Product $product)
    {
        $user = Auth::user();

        if (!$product->isStateSelling) {
            abort(404);
        }

        $token = $request->input('card-token');

        try {
            $this->settlement($product->id, $product->seller->id, $user->id, $token);
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()
                ->with('type', 'danger')
                ->with('message', '購入処理が失敗しました。');
        }

        return redirect()->route('product', [$product->id])
            ->with('message', '商品を購入しました。');
    }

    /**
     * 決算処理
     * @param $productID, $sellerID, $buyerID, $token
     *
     */
    private function settlement($productID, $sellerID, $buyerID, $token)
    {
        DB::beginTransaction();

        try {
            // 多重決済を避けるためにレコードを排他ロック
            $seller = User::lockForUpdate()->find($sellerID);
            $product   = Product::lockForUpdate()->find($productID);

            if ($product->isStateBought) {
                throw new \Exception('多重決済');
            }

            $product->state     = Product::STATE_BOUGHT;
            $product->bought_at = Carbon::now();
            $product->buyer_id  = $buyerID;
            $product->save();

            $charge = Charge::create([
                'card'     => $token,
                'amount'   => $product->price,
                'currency' => 'jpy'
            ]);
            if (!$charge->captured) {
                throw new \Exception('支払い確定失敗');
            }

            $seller->sales += $product->price;
            $seller->save();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();
    }
}
