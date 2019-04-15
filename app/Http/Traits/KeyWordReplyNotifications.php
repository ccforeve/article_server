<?php

namespace App\Http\Traits;

use App\Models\Product;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;

trait KeyWordReplyNotifications
{
    use FirstRuleNotifications, SecondRuleNotifications, ThirdRuleNotifications;

    protected $domain = "http://btl.yxcxin.com/article_detail";
    /**
     * 处理关键词自动回复
     * @param $content
     * @return string
     */
    public function searchProduct( $openid, $content )
    {
        /***********第一规则***********/
        if($first = $this->first($content)) {
            return $first;
        }
        /***********第二规则***********/
        if($second = $this->second($content)) {
            return $second;
        }
        /***********第三规则***********/
        return $this->third($content);
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
            $message .= "{$key}、[{$product->id}]<a href='" . $this->url("{$this->domain}/{$product->article->id}/public") . "'>{$product->name}</a>(零售：{$product->price}元，会员：{$member_price}元 + {$product->ticket}卷)\n";
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
