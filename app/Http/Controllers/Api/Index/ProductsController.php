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
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductsController extends Controller
{
    /**
     * 产品搜索列表
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function searchList( Request $request )
    {
        $search_key = $request->search_key;
        $products = Product::with('article:id,product_id')
            ->where([['state', '<>', 9], ['is_show_price', '=', 1]])
            ->where(function ($query) use ($search_key) {
                $query->where('alias_name', 'like', "%{$search_key}%")->orWhere('desc', 'like', "%{$search_key}%");
            })
            ->latest('listed_at')
            ->paginate(10);

        return $products;
    }

    /**
     * 添加产品
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function store( Request $request, Product $product )
    {
        $has_product = Product::query()->where('online_id', $request->online_id)->first();
        if($has_product) {
            info('同步产品', [$has_product->id]);
            $has_product->update($request->all());
            //修改文章
            $content = "零售：{$product->price}元，会员：{$product->money}元 + {$product->ticket}券";
            if($product->kind == 1) {
                if($product->price == $product->money) {
                    $content = "会员价：{$product->money}元";
                } else {
                    $content = "零售：{$product->price}元，会员价：{$product->money}元";
                }
            }
            Article::query()->where('product_id', $has_product->id)->update([
                'title' => $request->name,
                'cover' => $request->cover,
                'desc' => $content,
                'detail' => $request->content
            ]);
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

    /**
     * 更新预售产品为已发售
     * @param Request $request
     * @return mixed
     */
    public function updateProducts( Request $request )
    {
        $online_ids = explode(',', $request->online_ids);
        foreach ($online_ids as $id) {
            Product::query()->where('online_id', $id)->update(['kind' => 0]);
        }

        return $this->response->array([
            'code' => 201,
            'message' => '更新产品类型成功'
        ]);
    }

    /**
     * 分类的产品列表
     * @param $category_id
     * @return mixed
     */
    public function list( Request $request, $category_id )
    {
        $category = ProductCategory::query()->where('online_id', $category_id)->first();
        if ($request->has('user_id')) {
            $user_id = $request->user_id;
            $products = Product::with(
                [
                    'article:id,product_id',
                    'collection' => function ($query) use ($user_id) {
                        $query->where('user_id', $user_id)->select('id', 'user_id', 'product_id');
                    }
                ]
            );
        } else {
            $products = Product::with('article:id,product_id');
        }
        $products = $products->where(['parent_category_id' => $category_id, 'is_show_price' => 1])
            ->select('id', 'cover', 'name', 'kind', 'state', 'price', 'money', 'ticket')
            ->latest('listed_at')
            ->paginate(20);

        return $this->response->array([
            'products' => $products,
            'category' => $category
        ]);
    }
}
