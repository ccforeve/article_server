<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Poster extends Model
{
    public function poster()
    {
        return $this->morphTo();
    }

    public function getImageUrlAttribute( $value )
    {
        if(!str_contains($value, 'http')) {
            return Storage::disk('admin')->url($value);
        }
        return $value;
    }
}
