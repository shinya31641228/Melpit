<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoughtProductsController extends Controller
{
    /**
     * 購入商品一覧を表示する
     *
     * @return array 購入商品一覧
     */
    public function showPurchaseList(){
        $user = Auth::user();

        $products = $user->boughtProducts()->orderBy('id', 'DESC')->get();

        return view('products.purchase-list')->with('products', $products);
    }
}
