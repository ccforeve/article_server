<?php

namespace App\Http\Requests;

class CollectorRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:15',
            'desc' => 'max:500'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => '标题不能为空',
            'title.max' => '标题最长15字',
            'desc.max' => '描述最长500字'
        ];
    }
}
