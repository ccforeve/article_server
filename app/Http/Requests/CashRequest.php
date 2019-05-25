<?php

namespace App\Http\Requests;

class CashRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "price" => "required|integer|min:1|max:200"
        ];
    }

    public function messages()
    {
        return [
            'price.required' => '提现金额不能为空',
            'price.integer' => '提现金额必须正整数',
            'price.min' => '最小提现金额为100',
            'price.max' => '最大提现金额为200'
        ];
    }
}
