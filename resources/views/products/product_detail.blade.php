@extends('layouts.app')

@section('title')
{{$product->name}} | 商品詳細
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-8 offset-2 bg-white">
            <div class="row mt-3">
                <div class="col-8 offset-2">
                    @if (session('message'))
                    <div class="alert alert-{{ session('type', 'success') }}" role="alert">
                        {{ session('message') }}
                    </div>
                    @endif
                </div>
            </div>

            @include('products.product_detail_panel', [
            'product' => $product
            ])

            <div class="row">
                <div class="col-8 offset-2">
                    @if ($product->isStateSelling)
                    <a href="{{route('product.buy', [$product->id])}}" class="btn btn-secondary btn-block">購入</a>
                    @else
                    <button class="btn btn-dark btn-block" disabled>売却済み</button>
                    @endif
                </div>
            </div>

            <div class="my-3">{!! nl2br(e($product->description)) !!}</div>
        </div>
    </div>
</div>
@endsection
