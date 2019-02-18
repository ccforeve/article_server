<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = ['state', 'pay_at', 'refund_state'];

    public function scopePay( $query )
    {
        return $query->where(['state' => 1, 'refund_state' => 0,]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function s_user()
    {
        return $this->belongsTo(User::class, 'superior');
    }

    public function sup_user()
    {
        return $this->belongsTo(User::class, 'superior_up');
    }
}
