<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * userに紐づく出品済み商品を取得
     *
     * @return int id
     */

    public function soldProducts(){
        return $this->hasMany(Product::class,'seller_id','id');
    }

    /**
     * userに紐づく購入済み商品を取得
     *
     * @return int id
     */

    public function boughtProducts(){
        return $this->hasMany(Product::class,'buyer_id','id');
    }
}
