<?php


namespace App\Http\Controllers\Api\Index;

use App\Http\Controllers\Api\Controller;
use App\Jobs\CachePosters;
use App\Models\Article;
use App\Models\Footprint;
use App\Models\KeyWordCustom;
use App\Models\Product;
use App\Models\UserArticle;
use Carbon\Carbon;

class TestsController extends Controller
{
    public function test()
    {
//        $products = Product::query()->get();
//        foreach ($products as $key => $product) {
//            $content = "零售：{$product->price}元，会员：{$product->money}元 + {$product->ticket}券";
//            if($product->kind == 1) {
//                if($product->price == $product->money) {
//                    $content = "会员：{$product->money}元";
//                } else {
//                    $content = "零售：{$product->price}元，会员：{$product->money}元";
//                }
//            }
//            Article::query()->where('product_id', $product->id)->update([
//                'desc' => $content
//            ]);
//        }
    }
}
