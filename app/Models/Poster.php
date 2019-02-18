<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poster extends Model
{
    public function poster()
    {
        return $this->morphTo();
    }
}
