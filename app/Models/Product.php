<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable = [
        'seller_id',
        'secondary_category_id',
        'product_condition_id',
        'name',
        'image_file_name',
        'description',
        'price',
        'state',
    ];

    //出品中
    const STATE_SELLING = 'selling';
    //購入済み
    const STATE_BOUGHT = 'bought';

    //出品中
    public function getIsStateSellingAttribute()
    {
        return $this->state === self::STATE_SELLING;
    }

    //購入済み
    public function getIsStateBoughtAttribute()
    {
        return $this->state === self::STATE_BOUGHT;
    }

    //bought_atカラムを取り出す際にdatetime(Carbonクラス)に変換
    protected $casts = [
        'bought_at' => 'datetime',
    ];
    


    public function secondaryCategory()
    {
        return $this->belongsTo(SecondaryCategory::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function condition()
    {
        return $this->belongsTo(ProductCondition::class, 'product_condition_id');
    }
}
