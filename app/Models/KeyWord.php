<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class KeyWord extends Model
{
    protected $guarded = [];

    public function custom(  )
    {
        return $this->belongsTo(KeyWordCustom::class, 'custom_id');
    }

    public function scopeType( $query, $value )
    {
        $query->where('type', $value);
    }
}
