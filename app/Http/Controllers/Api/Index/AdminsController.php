<?php


namespace App\Http\Controllers\Api\Index;


use App\Http\Controllers\Api\Controller;
use Encore\Admin\Auth\Database\Administrator;

class AdminsController extends Controller
{
    public function admin($admin_id)
    {
        $qrcode = \Encore\Admin\Auth\Database\Administrator::query()->where('id', $admin_id)->value('qrcode');

        return $this->response->array([
            'qrcode' => \Storage::disk('admin')->url($qrcode)
        ]);
    }
}