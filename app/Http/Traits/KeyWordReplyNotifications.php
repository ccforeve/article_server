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
    public function searchProduct( $content )
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
        $message = "智能推荐关键词为“{$content}”的产品{$products->total()}种";
        if($products->total() > 5) {
            $message .= "，<a href='http://btl.yxcxin.com/productList?search_key=". $content ."'>点此查看全部检索结果</a>，下面仅显示5条检索结果：\n";
        } else {
            $message .= "：\n";
        }
        foreach ( $products->items() as $key => $product ) {
            $tip = "";
            if($product->kind == 1){
                $tip = "[复]";
            } elseif($product->kind == 3) {
                $tip = "[预]";
            }
            $key++;
            $message .= "{$key}、[{$product->online_id}]<a href='" . $this->url("{$this->domain}/{$product->article->id}/public") . "'>{$tip}{$product->name}</a>(零售：{$product->price}元，会员：{$product->money}元 + {$product->ticket}券)\n";
        }

        return $message;
    }

    /**
     * 发送图片消息
     * @param $item
     * @return News
     */
    public function sendNewItem( $item )
    {
        $content = $this->productDesc($item);
        $item = [
            new NewsItem([
                'title' => $item->name,
                'description' => $content,
                'url' => "http://btl.yxcxin.com/article_detail/{$item->article->id}/public",
                'image' => "http:" . str_replace('/p/', '/pxs/', $item->cover)
            ])
        ];
        return new News($item);
    }

    public function productDesc($product)
    {
        $content = "零售：{$product->price}元，会员：{$product->money}元 + {$product->ticket}券";
        if($product->kind == 1) {
            if($product->price == $product->money) {
                $content = "会员价：{$product->money}元";
            } else {
                $content = "零售：{$product->price}元，会员价：{$product->money}元";
            }
        }

        return $content;
    }
}
