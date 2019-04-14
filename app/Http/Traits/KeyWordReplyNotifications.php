<?php


namespace App\Http\Traits;


use App\Models\KeyWord;
use App\Models\Product;
use Carbon\Carbon;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;

trait KeyWordReplyNotifications
{
    /**
     * 处理关键词自动回复
     * @param $content
     * @return string
     */
    public function searchProduct( $openid, $content )
    {
        $equal_word = KeyWord::query()->where(function ($query) use ($content) {
            $query->where('name', $content)->orWhere('cmd', $content);
        })->type(0)->first();
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
                return $this->sendCustomMessage($equal_word->custom->response_content);
            }
        }
        //循环关键词表对比
        $key_words = KeyWord::query()->get();
        foreach ($key_words as $key => $key_word) {
            $type = $key_word->type;
            if($type == 1) {
                if ( str_contains($content, $key_word->name) ) {
                    $search_key = str_replace($key_word->name, '', $content);
                    $products = Product::query()->where('alias_name', 'like', "%$search_key%")->paginate(7);
                    if ( $products->total() > 0 ) {
                        return $this->sendProductsMessage($products, $search_key);
                    }
                }
            } elseif ($type == 2) {
                $key_arr = explode("|", $key_word->name);
                $count = 0;
                foreach ($key_arr as $k => $item) {
                    if(str_contains($content, $item)) {
                        $count++;
                    }
                }
                if(count($key_arr) == $count) {
                    return $this->sendCustomMessage($key_word->custom->response_content);
                }
            }
        }
        //返回产品信息
        $product_query = Product::with('article:id,product_id');
        if($content > 0) {
            $product = $product_query->where('id', $content)->first();
            return $this->sendNewItem($product);
        } else {
            $client = new \AipNlp(config('app.baidu_api.app_id'), config('app.baidu_api.app_key'), config('app.baidu_api.app_secret'));
            $baidu_api_result = $client->lexerCustom($content);
            $max_key = getMaxString($baidu_api_result['items'])['item'];
            $products = $product_query->where('alias_name', 'like', "%{$max_key}%")->paginate(6);
            if ( $products->total() > 0 ) {
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
     * 回复消息类型
     * @param $message
     * @return array|Image|News
     */
    public function sendCustomMessage( $message )
    {
        $message = explode('|', $message);
        switch (count($message)) {
            case 1:
                return $message[0];
            case 2:
                return new Image($message[1]);
            case 3:
                $item = [
                    new NewsItem([
                        'title' => $message[0],
                        'description' => '点开打开图片，然后按住图片或以保存',
                        'url' => $message[1],
                        'image' => $message[2]
                    ])
                ];
                return new News($item);
            case 4:
                $item = [
                    new NewsItem([
                        'title' => $message[0],
                        'description' => $message[3],
                        'url' => $message[1],
                        'image' => $message[2]
                    ])
                ];
                return new News($item);
        }
    }

    /**
     * 发送产品列表
     * @param $products
     * @param $content
     * @return string
     */
    public function sendProductsMessage( $products, $content )
    {
        $message = "智能推荐关键词为“{$content}”的产品{$products->total()}种：\n";
        foreach ( $products->items() as $key => $product ) {
            $key++;
            $member_price = number_format($product->price - $product->ticket, 2);
            $message .= "{$key}、[{$product->id}]<a href='" . $this->url("http://btl.yxcxin.com/article_detail/{$product->article->id}/public") . "'>{$product->name}</a>(零售：{$product->price}元，会员：{$member_price}元 + {$product->ticket}卷)\n";
        }

        return $message;
    }

    /**
     * 发送图片消息
     * @param $openid
     * @param $item
     * @return News
     */
    public function sendNewItem( $item )
    {
        $member_fee = number_format($item->price - $item->ticket, 2);
        $item = [
            new NewsItem([
                'title' => $item->name,
                'description' => "零售：{$item->price}，会员：{$member_fee} + {$item->ticket}卷",
                'url' => "http://btl.yxcxin.com/article_detail/{$item->article->id}/public",
                'image' => $item->cover
            ])
        ];
        return new News($item);
    }
}
