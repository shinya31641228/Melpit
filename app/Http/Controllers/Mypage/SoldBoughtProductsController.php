<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SoldBoughtProductsController extends Controller
{
    /**
     * 出品商品一覧を表示する
     *
     * @return view
     */
    public function showListingList(){
        $user = Auth::user();

        $products = $user->soldProducts()->orderBy('id', 'DESC')->get();

        return view('mypage.listing_list')->with('products', $products);
    }
}
