<?php


namespace App\Http\Controllers\Api\Index;

use App\Http\Controllers\Api\Controller;
use App\Jobs\CachePosters;
use App\Models\KeyWordCustom;
use App\Models\Product;
use Carbon\Carbon;

class TestsController extends Controller
{
    public function test()
    {
//        dispatch(new CachePosters());
    }
}
