<?php
namespace App\Http\Traits;


trait WechatNotifiable
{
    use CreateOrCheckUserNotifications, KeyWordReplyNotifications;
}
