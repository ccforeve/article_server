<?php

namespace App\Services;

use App\Models\Punch;
use Carbon\Carbon;
use App\Models\User;

class PunchService
{
    /**
     * 获取打卡状态
     *
     * @param User $user
     * @param Carbon $now
     *
     * @return int
     */
    public function getState(User $user) : int
    {
        $now = now();
        // 打卡起始时间
        $punchStart = Carbon::createFromTime(6, 00, 0);
        $punchEnd = Carbon::createFromTime(23, 00, 0);

        // 未关注公众号
        if (! $user->subscribe) {
            return 0;
        }

        // 已打卡
        $punch = Punch::query()->where('user_id', $user->id)->punch()->first();
        if ($punch) {
            return 1;
        }

        // 可以打卡
        if ($now->between($punchStart, $punchEnd)) {
            return 2;
        }

        // 未到打卡时间
        if ($now->lt($punchStart)) {
            return 3;
        }

        // 已过打卡时间
        return 4;
    }
}
