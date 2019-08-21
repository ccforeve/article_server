<?php

namespace App\Http\Requests;

use Dingo\Api\Http\FormRequest;

class CollectionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'collector_id' => 'required|exists:collector,id'
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => '收藏产品不能为空',
            'product_id.exists' => '收藏的产品找不到',
            'collector_id.required' => '收藏夹不能为空',
            'collector_id.exists' => '找不到收藏夹'
        ];
    }
}
