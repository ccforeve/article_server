<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    /**
     * 要触发的所有关联关系
     *
     * @var array
     */
    protected $touches = ['collector'];

    protected $guarded = [];

    public function collector()
    {
        return $this->belongsTo(Collector::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
