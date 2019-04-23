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
            if($product) {
                return $this->sendNewItem($product);
            }
        }
        //直接查询别名
        $products = $product_query
            ->where([['state', '<>', 9], ['is_show_price', '=', 1]])
//            ->where('alias_name', 'like', "%{$content}%")
            ->where(function ($where) use ($content){
                $where->where('alias_name', 'like', "%{$content}%")->orWhere('desc', 'like', "%{$content}%");
            })
            ->latest('listed_at')
            ->paginate(5);
        if(count($products->items()) > 1) {
            return $this->sendProductsMessage($products, $content);
        }
        //百度分词后查询别名
        $client = new \AipNlp(config('app.baidu_api.app_id'), config('app.baidu_api.app_key'), config('app.baidu_api.app_secret'));
        $baidu_api_result = $client->lexerCustom($content);
        info('关键词', [$content, $baidu_api_result]);
        $max_key = getMaxString($baidu_api_result['items'])['item'];
        $products = Product::with('article:id,product_id')
            ->where([['state', '<>', 9], ['is_show_price', '=', 1]])
//            ->where('alias_name', 'like', "%{$max_key}%")
            ->where(function ($where) use ($max_key){
                $where->where('alias_name', 'like', "%{$max_key}%")->orWhere('desc', 'like', "%{$max_key}%");
            })
            ->latest('listed_at')
            ->paginate(5);
        if ( $products->total() > 0 ) {
            return $this->sendProductsMessage($products, $max_key);
        } else {
            return "未检索到关键词为“{$content}”的产品，可以修改关键词，重新检索！";
        }
    }
}
