<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/25 0025
 * Time: 下午 4:04
 */

namespace App\Http\Controllers\Api\Index;


use Illuminate\Http\Request;

class Authorization extends BaseController
{
    public function check( Request $request )
    {
        if($token = $request->token) {
            return $this->response->array([
                'code' => 200,
                'ref_token' => $token
            ]);
        }
        return $this->response->noContent();
    }
}
