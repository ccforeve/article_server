<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['type', 'integral_scale', 'member_up_at', 'member_lock_at', 'state'];

    public function superiorUser()
    {
        return $this->belongsTo(User::class, 'superior');
    }

    public function superiorUpUser()
    {
        return $this->belongsTo(User::class, 'superior_up');
    }


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
