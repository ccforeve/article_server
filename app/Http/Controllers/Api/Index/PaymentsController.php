<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/10 0010
 * Time: 下午 3:55
 */

namespace App\Http\Controllers\Api\Index;


use App\Http\Controllers\Api\Controller;
use App\Models\Payment;
use App\Transformers\PaymentTransformer;

class PaymentsController extends Controller
{
    public function list()
    {
        $payments = Payment::all();

        return $this->response->collection($payments, new PaymentTransformer());
    }
}
