<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/25 0025
 * Time: 下午 4:04
 */

namespace App\Http\Controllers\Api\Index;

use Auth;

class Authorization extends BaseController
{
    public function refreshToken()
    {
        try {
            $token = Auth::guard('api')->refresh();

            return $this->response->array([
                'code' => 200,
                'access_token' => 'Bearer ' . $token,
                'expires_in' => 7200
            ]);
        } catch (\Exception $e) {
            abort(401);
        }
    }
}
