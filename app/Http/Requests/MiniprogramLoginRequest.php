<?php

namespace App\Http\Requests;

class MiniprogramLoginRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'session_key' => 'required',
            'iv' => 'required',
            'encryptedData' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'session_key.required' => '小程序登录session_key不可为空',
            'iv.required' => 'iv不可为空',
            'encryptedData.required' => 'encrypted不可为空',
        ];
    }
}
