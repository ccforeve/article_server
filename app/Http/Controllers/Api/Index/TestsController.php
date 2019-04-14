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
//        $client = new \AipNlp(config('app.baidu_api.app_id'), config('app.baidu_api.app_key'), config('app.baidu_api.app_secret'));
//        $result = $client->lexerCustom('给我查一下洗发水');
//        dd(getMaxString($result['items'])['item']);
        $client = new \AipNlp(config('app.baidu_api.app_id'), config('app.baidu_api.app_key'), config('app.baidu_api.app_secret'));
        $baidu_api_result = $client->lexerCustom('给我查一下洗发水');
        $max_key = getMaxString($baidu_api_result['items'])['item'];
        dd($max_key);
    }
}
