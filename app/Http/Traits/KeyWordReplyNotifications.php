<?php


namespace App\Http\Traits;


use App\Models\KeyWord;
use App\Models\Product;
use Carbon\Carbon;

trait KeyWordReplyNotifications
{
    /**
     * 处理关键词自动回复
     * @param $content
     * @return string
     */
    public function searchProduct( $openid, $content )
    {
        info($content);
        $equal_word = KeyWord::query()->where(function ($query) use ($content) {
            $query->where('name', $content)->orWhere('cmd', $content);
        })->where('type', 0)->first();
        if($equal_word) {
            switch ($equal_word->cmd) {
                case 'jt':
                    $date_string = '今日';
                    return $this->dateSearchProduct(today()->toDateString(), $date_string);
                case 'jr':
                    $date_string = '今日';
                    return $this->dateSearchProduct(today()->toDateString(), $date_string);
                case 'zt':
                    $date_string = '昨日';
                    return $this->dateSearchProduct(Carbon::yesterday()->toDateString(), $date_string);
                case 'zr':
                    $date_string = '昨日';
                    return $this->dateSearchProduct(Carbon::yesterday()->toDateString(), $date_string);
                case 'zb':
                    $date_string = '本周';
                    $date = [Carbon::parse('last week')->startOfDay(), Carbon::parse('this week')->startOfDay()];
                    return $this->dateSearchProduct($date, $date_string, 'between');
                case 'sz':
                    $date_string = '上周';
                    $date = [Carbon::parse('last week')->startOfDay(), Carbon::parse('this week')->startOfDay()];
                    return $this->dateSearchProduct($date, $date_string, 'between');
            }
            if($equal_word->custom_id) {
                return $equal_word->custom->response_content;
            }
        }
        $client = new \AipNlp(config('app.baidu_api.app_id'), config('app.baidu_api.app_key'), config('app.baidu_api.app_secret'));
        $result = $client->lexer($content);
        foreach ($result['items'] as $key => $item) {
//            KeyWord::query()->where('')->
        }
        //返回产品信息
        $product_query = Product::with('article:id,product_id');
        $content = (int) $content;
        if(is_int($content) && $content > 0) {
            $product = $product_query->where('id', $content)->first();
            $this->sendNewItem($openid, $product);
        } else {
            $products = $product_query->where('name', 'like', "%$content%")->paginate(6);
            if ( count($products->items()) > 1 ) {
                $message = "智能推荐关键词为“{$content}”的产品{$products->total()}种：\n";
                foreach ( $products->items() as $key => $product ) {
                    $key++;
                    $member_price = number_format($product->price - $product->ticket, 2);
                    $message .= "{$key}、[{$product->id}]<a href='" . $this->url("http://btl.yxcxin.com/article_detail/{$product->article->id}/public") . "'>{$product->name}</a>(零售：{$product->price}元，会员：{$member_price}元 + {$product->ticket}卷)\n";
                }

                return $message;
            } else {
                return "智能搜索暂无“{$content}”";
            }
        }
    }

    /**
     * 根据时间查询产品
     * @param $date
     * @param int $type
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function dateSearchProduct( $date, $date_string, $type = 0 )
    {
        $product_query = Product::query();
        if(!$type) {
            $product_query->where('listed_at', $date);
        } else {
            $product_query->whereBetween('listed_at', $date);
        }
        $products = $product_query->paginate(6);
        if(count($products->items()) < 1) {
            $is_today = $date == now()->toDateString() ? "（截止到现在）" : '';

            return "绿叶商城{$date_string}未上架新产品" . $is_today;
        }

        return $products;
    }

    /**
     * 发送图片消息
     * @param $openid
     * @param $item
     */
    public function sendNewItem( $openid, $item )
    {
        $member_fee = number_format($item->price - $item->ticket, 2);
        $data = [
            'title' => $item->name,
            'description' => "零售：{$item->price}，会员：{$member_fee} + {$item->ticket}卷",
            'url' => "http://btl.yxcxin.com/article_detail/{$item->article->id}/public",
            'image' => $item->cover
        ];
        message($openid, 'new_item', $data);
    }
}
