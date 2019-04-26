<?php


namespace App\Http\Traits;


use App\Models\KeyWord;
use App\Models\Product;
use Carbon\Carbon;

trait FirstRuleNotifications
{
    public function first( $content )
    {
        $equal_word = KeyWord::query()->where(function ($query) use ($content) {
            $query->where('name', $content)->orWhere('cmd', $content);
        })->type(0)->first();
        if($equal_word) {
            switch ($equal_word->cmd) {
                case 'jt':
                    return $this->today();
                case 'jr':
                    return $this->today();
                case 'zt':
                    return $this->yesterday();
                case 'zr':
                    return $this->yesterday();
                case 'bz':
                    return $this->thisWeek();
                case 'sz':
                    return $this->lastWeek();
            }
            if($equal_word->custom_id) {
                return $this->sendCustomMessage($equal_word->custom->response_content);
            }
        }

        return false;
    }

    public function today()
    {
        $undata_string = "绿叶商城今日未上架新产品（截止到现在）";
        $data_string = "今日绿叶商城上架新产品";
        $result = $this->dateSearchProduct(today()->toDateString());
        return $this->checkData($result, $undata_string, $data_string);
    }

    public function yesterday()
    {
        $undata_string = "绿叶商城昨日未上架新产品";
        $data_string = "昨日绿叶商城上架新产品";
        $result = $this->dateSearchProduct(Carbon::yesterday()->toDateString());
        return $this->checkData($result, $undata_string, $data_string);
    }

    public function thisWeek()
    {
        $comment = "[{$this->this_week->format('m-d')} - {$this->next_week->subDay()->format('m-d')}]";
        $undata_string = "本周{$comment}绿叶商城未上架新产品（截止到现在）";
        $data_string = "本周{$comment}绿叶商城上架新产品";
        $between_time = [$this->this_week, $this->next_week];
        $result = $this->dateSearchProduct($between_time, 1);
        return $this->checkData($result, $undata_string, $data_string);
    }

    public function lastWeek()
    {
        $comment = "[{$this->last_week->format('m-d')} - {$this->this_week->subDay()->format('m-d')}]";
        $undata_string = "上周{$comment}绿叶商城未上架新产品";
        $data_string = "上周{$comment}绿叶商城上架新产品";
        $between_time = [$this->last_week, $this->this_week];
        $result = $this->dateSearchProduct($between_time, 1);
        return $this->checkData($result, $undata_string, $data_string);
    }

    /**
     * 根据时间查询产品
     * @param $date
     * @param int $type
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function dateSearchProduct( $date, $type = 0 )
    {
        $product_query = Product::query();
        if(!$type) {
            $product_query->whereDate('listed_at', $date);
        } else {
            $product_query->whereBetween('listed_at', $date);
        }
        $products = $product_query->paginate(6);
        if(count($products->items()) < 1) {
            return null;
        }

        return $products;
    }

    public function checkData( $data, $nodata_string, $data_string )
    {
        if(!$data) {
            return $nodata_string;
        }
        $message = "发送序号后面【中括号】里的数字，查看产品信息。\n========================\n{$data_string}{$data->total()}种：\n";
        foreach ( $data->items() as $key => $product ) {
            $key++;
            $member_price = number_format($product->price - $product->ticket, 2);
            $message .= "{$key}、[{$product->online_id}]<a href='" . $this->url("{$this->domain}/{$product->article->id}/public") . "'>{$product->name}</a>(零售：{$product->price}元，会员：{$member_price}元 + {$product->ticket}卷)\n";
        }
        return $message;
    }
}
