<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Http\File;
use App\Http\Requests\SellRequest;
use App\Models\ProductCondition;
use App\Models\PrimaryCategory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class SellController extends Controller
{
    /**
     * 商品出品画面を表示
     *
     * @return view $conditions $categories
     */
    public function showSellForm(){

        $categories = PrimaryCategory::query()->with([
            'secondaryCategories' => function ($query) {
                $query->orderBy('sort_no');
            }
        ])
        ->orderBy('sort_no')
        ->get();

        $conditions = ProductCondition::orderBy('sort_no')->get();

        return view('sell',[
            'conditions' => $conditions,
            'categories' => $categories,
        ]);
    }
    /**
     * 商品出品する
     * @param SellRequest $request
     * @return string ステータス
     */
    public function sellProduct(SellRequest $request){
        $user = Auth::user();
        $product = new Product;
        $imageName = $this->saveImage($request->file('item-image'));

        $product->fill([
            'seller_id' => $user->id,
            'secondary_category_id' => $request->input('category'),
            'product_condition_id' => $request->input('condition'),
            'name' => $request->input('name'),
            'image_file_name' => $imageName,
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'state' => Product::STATE_SELLING,
        ])
        ->save();

        return redirect()->back()->with([
            'status' => '商品を出品しました',
        ]);
    }

    /**
     * 商品画像をリサイズして保存します
     *
     * @param UploadedFile $file アップロードされたアバター画像
     * @return string ファイル名
     */
    private function saveImage(UploadedFile $file): string
    {
        $tempPath = $this->makeTempPath();

        Image::make($file)->fit(300, 300)->save($tempPath);

        $filePath = Storage::disk('public')
        ->putFile('item-images', new File($tempPath));

        return basename($filePath);
    }

    /**
     * 一時的なファイルを生成してパスを返します。
     *
     * @return string ファイルパス
     */
    private function makeTempPath(): string
    {
        $tmp_fp = tmpfile();
        $meta   = stream_get_meta_data($tmp_fp);
        return $meta["uri"];
    }
}
