<?php

namespace App\Http\Requests;

class MiniprogramPayRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required',
            'order_id' => 'required|exists:orders,id',
            'openid' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => '支付用户不可为空',
            'order_id.exists' => '没有支付订单',
            'openid.required' => 'openid不可为空',
        ];
    }
}
