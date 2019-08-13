<?php


namespace App\Models;


use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Model;

class Presale extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(\Encore\Admin\Auth\Database\Administrator::class, 'admin_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}