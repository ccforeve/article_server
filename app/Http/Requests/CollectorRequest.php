<?php

namespace App\Http\Requests;

use Dingo\Api\Http\FormRequest;

class CollectorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:32',
            'desc' => 'max:200'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => '标题不能为空',
            'title.max' => '标题最长32字',
            'desc.max' => '描述最长200字'
        ];
    }
}
