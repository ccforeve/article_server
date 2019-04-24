<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/18 0018
 * Time: 下午 1:58
 */

namespace App\Http\Controllers\Api\Index;


use App\Http\Controllers\Api\Controller;
use App\Models\Article;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductsController extends Controller
{
    public function searchList( Request $request )
    {
        $search_key = $request->search_key;
        $products = Product::query()
            ->where([['state', '<>', 9], ['is_show_price', '=', 1]])
            ->where(function ($query) use ($search_key) {
                $query->where('alias_name', 'like', "%{$search_key}%")->orWhere('desc', 'like', "%{$search_key}%");
            })->paginate(10);

        return $products;
    }

    public function store( Request $request, Product $product )
    {
        $has_product = Product::query()->where('online_id', $request->online_id)->first();
        if($has_product) {
            $has_product->update($request->all());
        } else {
            $product->fill($request->all());
            $product->save();

            if (!$product->id) {
                return response()->json(['content' => '添加出错'], Response::HTTP_EXPECTATION_FAILED);
            }

            //添加文章
            $content = "零售：{$product->price}元，会员：{$product->money}元 + {$product->ticket}券";
            if($product->kind == 1) {
                if($product->price == $product->money) {
                    $content = "会员价：{$product->money}元";
                } else {
                    $content = "零售：{$product->price}元，会员价：{$product->money}元";
                }
            }
            Article::create([
                'title' => $request->name,
                'cover' => $request->cover,
                'category_id' => 1,
                'brand_id' => 1,
                'product_id' => $product->id,
                'desc' => $content,
                'detail' => $request->content
            ]);
        }

        return response()->json([
            'content' => '添加成功'
        ], Response::HTTP_CREATED);
    }
}
