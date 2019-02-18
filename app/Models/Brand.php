<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    public function posters()
    {
        return $this->morphMany(Poster::class, 'poster');
    }
}
