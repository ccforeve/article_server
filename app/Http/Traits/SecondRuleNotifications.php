<?php


namespace App\Http\Traits;


use App\Models\KeyWord;
use App\Models\Product;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;

trait SecondRuleNotifications
{
    public function second( $content )
    {
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

        return false;
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
}
