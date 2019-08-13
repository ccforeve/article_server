<?php


namespace App\Services;


use Carbon\Carbon;

class BaseService
{
    /**
     * 判断会员是否过期
     * @param $member_time
     * @return bool
     */
    public function checkMember($member_time)
    {
        if(Carbon::parse($member_time)->gt(now())) {
            return true;
        }

        return false;
    }
}