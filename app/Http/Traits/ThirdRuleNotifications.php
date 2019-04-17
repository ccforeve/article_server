<?php


namespace App\Http\Traits;


use App\Models\Product;

trait ThirdRuleNotifications
{
    public function third( $content )
    {
        //返回产品信息
        $product_query = Product::with('article:id,product_id');
        if($content > 0) {  //直接查询online_id
            $product = $product_query->where('online_id', $content)->first();
            return $this->sendNewItem($product);
        }
        //直接查询别名
        $products = $product_query->where('alias_name', 'like', "%{$content}%")->paginate(6);
        if(count($products->items()) > 1) {
            return $this->sendProductsMessage($products, $content);
        }
        //百度分词后查询别名
        $client = new \AipNlp(config('app.baidu_api.app_id'), config('app.baidu_api.app_key'), config('app.baidu_api.app_secret'));
        $baidu_api_result = $client->lexerCustom($content);
        $max_key = getMaxString($baidu_api_result['items'])['item'];
        $products = Product::with('article:id,product_id')->where('alias_name', 'like', "%{$max_key}%")->paginate(6);
        if ( $products->total() > 0 ) {
            return $this->sendProductsMessage($products, $max_key);
        } else {
            return "智能搜索暂无“{$content}”";
        }
    }
}
