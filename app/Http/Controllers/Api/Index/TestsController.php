<?php


namespace App\Http\Controllers\Api\Index;

use App\Http\Controllers\Api\Controller;
use App\Models\KeyWordCustom;
use App\Models\Product;
use Carbon\Carbon;

class TestsController extends Controller
{
    public function test()
    {
        dd(is_int(1) && 1 > 0);
    }
}
