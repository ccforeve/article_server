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
    public function list(  )
    {
        $categories = ProductCategory::query()
            ->where(['parent_id' => 0, 'level' => 1])
            ->whereNotIn('id', [228, 231])
            ->get(['online_id', 'name']);

        return $this->response->array([
            'data' => $categories
        ], 200);
    }

    /**
     * 添加产品分类
     * @param Request $request
     * @param ProductCategory $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function store( Request $request, ProductCategory $category )
    {
        $has_category = ProductCategory::query()->where('online_id', $request->online_id)->first();
        if($has_category) {
            $has_category->update($request->all());
            return response()->json(['message' => '添加成功'], Response::HTTP_CREATED);
        } else {
            $category->fill($request->all());
            $category->save();
            if($category->id) {
                return response()->json(['message' => '添加成功'], Response::HTTP_CREATED);
            }
        }

        return response()->json(['content' => '添加出错'], Response::HTTP_EXPECTATION_FAILED);
    }
}
