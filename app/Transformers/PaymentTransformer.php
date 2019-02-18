<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/29 0029
 * Time: 上午 2:19
 */

namespace App\Transformers;

use App\Models\Payment;
use League\Fractal\TransformerAbstract;

class PaymentTransformer extends TransformerAbstract
{
    public function transform( Payment $payment )
    {
        return [
            'id' => $payment->id,
            'price' => $payment->price,
            'original_price' => $payment->original_price,
            'title' => $payment->title,
            'month' => $payment->month,
            'extension' => $payment->extension,
        ];
    }
}
