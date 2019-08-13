<?php

namespace App\Http\Requests;

class DistributionRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'admin_id' => 'required|integer',
            'ids' => 'array'
        ];
    }
}
