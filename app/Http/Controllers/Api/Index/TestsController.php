<?php


namespace App\Http\Controllers\Api\Index;

use App\Http\Controllers\Api\Controller;
use App\Jobs\UpdateUnionid;
use App\Models\ActivityDraw;
use App\Models\Presale;
use App\Models\User;
use Carbon\Carbon;
use EasyWeChat\OfficialAccount\Application;

class TestsController extends Controller
{
    public function test(Application $app)
    {
        if(!Presale::query()->where('user_id', 50)->value('id')) {
            $admin_id = \DB::table('admin_role_users')->where('role_id', 3)->inRandomOrder()->value('user_id');
            Presale::query()->create([
                'admin_id' => $admin_id,
                'user_id' => 50
            ]);
        }
    }
}
