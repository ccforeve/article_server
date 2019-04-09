<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/10 0010
 * Time: 下午 12:27
 */

namespace App\Http\Controllers\Api\Index;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use EasyWeChat\OfficialAccount\Application;
use Illuminate\Http\Request;

class WechatController extends Controller
{
    protected $app;

    public function __construct( Application $app )
    {
        $this->app = $app;
    }

    /**
     * 上传素材
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function uploadImage()
    {
        $material = $this->app->material;
        $result = $material->uploadImage(public_path('uploads/images/')."1.jpg");
        return $result;
    }

    /**
     * 清除缓存
     */
    public function reloadWechatShareConfig()
    {
        \Cache::flush();
    }

    public function config( Application $app, Request $request )
    {
        return $app->jssdk->buildConfig(['chooseWXPay', 'onMenuShareTimeline', 'onMenuShareAppMessage'], urldecode($request->url));
    }

    public function index()
    {
        $app = $this->app;
        $app->server->push(function ($message) {
            switch ($message['MsgType']) {
                //收到事件消息
                case 'event':
                    return $this->_event($message['FromUserName'],$message['Event'],$message['EventKey']);
                    break;
                //收到文字消息
                case 'text':
                    return '暂无客服';
                    break;
            }
        });

        // 将响应输出
        return $app->server->serve();
    }

    /**
     * 创建普通菜单
     */
    public function button()
    {
        $app = $this->app;
        $buttons = [
            [
                "type" => "view",
                "name" => "热文分享",
                "url"  => "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxfa7b58cf37b2d3bd&redirect_uri=http://btl.yxcxin.com&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect"
            ],
            [
                "type" => "view",
                "name" => "早起打卡",
                "url"  => "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxfa7b58cf37b2d3bd&redirect_uri=http://btl.yxcxin.com/punch&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect"
            ]
        ];
        $app->menu->create($buttons);
    }

    /**
     * 公众号事件推送分流
     * @param $FromUserName
     * @param $event
     * @param $eventkey
     * @return string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function _event($FromUserName, $event, $eventkey)
    {
        switch ($event) {
            //已关注公众号的
            case 'SCAN':
                if(is_numeric($eventkey)){
                    $user = $this->checkUser($FromUserName, $eventkey);
                    if($user->id == $eventkey) {
                        return '扫自己的推广二维码是没用的喔';
                    }
                }
                break;
            //未关注公众号的
            case 'subscribe':
                //扫推送二维码关注公众号（创建账号）
                if (strpos($eventkey, '_') !== false) {
                    $eventkey = str_replace('qrscene_', '', $eventkey);
                    $user = $this->checkUser($FromUserName, $eventkey);
                    if ($user->id == $eventkey) {
                        return '扫自己的推广二维码是没用的喔';
                    }
                } else {
                    $this->checkUser($FromUserName);
                }
                $context = "✨如何引流让客户主动加你？\n\n👉创业，不管产品再好，也要有人知道才行，所以很多创业者就为引流头痛，一方面要想着怎么加人，另一方面要想着怎么样才能留着住人。其实，这些问题在超级伙伴都可以轻松解决，为什么这么说？\n\n👉<a href='http://btl.yxcxin.com/punch'>【打卡】</a>每天努力打卡，积极向上，生成的打卡海报分享在朋友圈的传播中自动为你引来流量，根本不用你主动加人\n\n👉<a href='http://btl.yxcxin.com/poster'>【美图库】</a>提供整套的朋友圈素材，满足你的日常发圈需求，再也不用担心自己的客户因为朋友圈发的不好而把自己拉黑删除了";
                message($FromUserName, 'text', $context);
                break;
            //取消关注公众号
            case 'unsubscribe':
                User::where('openid', $FromUserName)->update(['subscribe' => 0]);
                break;
            case 'CLICK':
                return '暂无点击事件';
                break;
        }
    }

    /**
     * 检查用户账户情况
     * @param $FromUserName
     * @param $eventkey
     * @return mixed
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function checkUser( $FromUserName, $eventkey = 0 )
    {
        $user = User::where('openid', $FromUserName)->first();
        if($user) {
            if($eventkey) {
                $this->relation($user, $eventkey);
            }
             else {
                $user->subscribe = 1;
                $user->subscribe_at = Carbon::now()->toDateTimeString();
                $user->save();
            }
        } else {    //创建用户
            $user = $this->register($FromUserName, $eventkey);
        }
        return $user;
    }

    /**
     * 创建账户
     * @param $FromUserName     openid
     * @param $eventkey         上级推荐id
     * @return mixed
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function register($FromUserName, $eventkey = 0)
    {
        $user = $this->app->user->get($FromUserName);
        $data = [
            'openid' => $FromUserName,
            'nickname' => $user[ 'nickname' ],
            'avatar' => $user[ 'headimgurl' ],
            'subscribe' => 1,
            'subscribe_at' => now()->toDateTimeString(),
            'sex' => $user[ 'sex' ]
        ];
        $user = User::create($data);
        if($eventkey) {
            $puser = User::query()->where('id', $eventkey)->first(['id', 'superior']);
            $user->superior = $puser->id;
            $user->superior_up = $puser->superior;
            $user->extension_at = now()->toDateTimeString();
            $user->extension_type = '推广二维码';
            $user->save();
        }
        //保存用户
        return $user;
    }

    /**
     * 关联账户关系
     * @param $user
     * @param $eventkey
     */
    public function relation( $user, $eventkey )
    {
        if($user->id !== $eventkey && $user->extension_id == 0 && $user->type == 0) {
            $pinfo = User::find($eventkey);
            //当用户本来没有推广用户和经销商的时候
            $user->subscribe = 1;
            $user->subscribe_at = now()->toDateTimeString();
            $user->superior = $pinfo->id;
            $user->superior_up = $pinfo->superior;
            $user->extension_at = now()->toDateTimeString();
            $user->extension_type = '推广二维码';
            $user->save();
        }
    }
}
