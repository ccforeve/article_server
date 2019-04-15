<?php


namespace App\Http\Traits;


use App\Models\Product;

trait ThirdRuleNotifications
{
    public function third( $content )
    {
        //返回产品信息
        $product_query = Product::with('article:id,product_id');
        if($content > 0) {
            $product = $product_query->where('id', $content)->first();
            return $this->sendNewItem($product);
        }
        $client = new \AipNlp(config('app.baidu_api.app_id'), config('app.baidu_api.app_key'), config('app.baidu_api.app_secret'));
        $baidu_api_result = $client->lexerCustom($content);
        $max_key = getMaxString($baidu_api_result['items'])['item'];
        $products = $product_query->where('alias_name', 'like', "%{$max_key}%")->paginate(6);
        if ( $products->total() > 0 ) {
            return $this->sendProductsMessage($products, $max_key);
        } else {
            return "智能搜索暂无“{$content}”";
        }
    }
}
