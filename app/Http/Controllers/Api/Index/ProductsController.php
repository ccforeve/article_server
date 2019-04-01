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
    public function store( Request $request, Product $product )
    {
        $product->fill($request->all());
        $product->save();

        if(!$product->id) {
            return response()->json(['content' => '添加出错'], Response::HTTP_EXPECTATION_FAILED);
        }

        //添加文章
        Article::create([
            'title' => $request->name,
            'cover' => $request->cover,
            'category_id' => 1,
            'brand_id' => 1,
            'product_id' => $product->id,
            'detail' => $request->content
        ]);

        return response()->json([
            'content' => '添加成功'
        ], Response::HTTP_CREATED);
    }
}
