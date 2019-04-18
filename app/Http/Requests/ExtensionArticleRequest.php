<?php

namespace App\Http\Requests;

class ExtensionArticleRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "url" => "required|url"
        ];
    }

    public function messages()
    {
        return [
            'url.required' => '推荐文章链接不可为空',
            'url.url' => '不是正确的文章链接'
        ];
    }
}
