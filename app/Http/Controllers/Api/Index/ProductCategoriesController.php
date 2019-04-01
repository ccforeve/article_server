<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/20 0020
 * Time: 下午 4:57
 */

namespace App\Http\Controllers\Api\Index;


use App\Http\Controllers\Api\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductCategoriesController extends Controller
{
    public function store( Request $request, ProductCategory $category )
    {
        $category->fill($request->all());
        $category->save();
        if($category->id) {
            return response()->json(['message' => '添加成功'], Response::HTTP_CREATED);
        }

        return response()->json(['content' => '添加出错'], Response::HTTP_EXPECTATION_FAILED);
    }
}
