<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('top');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('products/{product}', 'ProductsController@showProductDetail')->name('product');
Route::get('/', 'ProductsController@showProducts')->name('top');


Route::middleware('auth')
    ->group(function(){
        Route::get('products/{product}/buy','ProductsController@showBuyProductForm')->name('product.buy');
        Route::post('products/{product}/buy','ProductsController@buyProduct')->name('product.buy');
        Route::get('sell','SellController@showSellForm')->name('sell');
        Route::post('sell','SellController@sellProduct')->name('sell');
    });

Route::prefix('mypage')
    ->namespace('MyPage')
    ->middleware('auth')
    ->group(function () {
        Route::get('edit-profile', 'ProfileController@showEditProfileForm')->name('mypage.edit-profile');
        Route::post('edit-profile', 'ProfileController@editProfileForm')->name('mypage.edit-profile');
        Route::get('listing-list', 'SoldBoughtProductsController@showListingList')->name('mypage.listing-list');
        Route::get('purchase-list', 'BoughtProductsController@showPurchaseList')->name('mypage.purchase-list');
    });
