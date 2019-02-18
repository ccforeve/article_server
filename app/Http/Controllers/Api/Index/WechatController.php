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
use EasyWeChat\Kernel\Messages\Image;
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
        return $app->jssdk->buildConfig(['updateAppMessageShareData', 'updateTimelineShareData'], $request->url);
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
//                    $content = '暂无客服';
//                    message($message['FromUserName'], 'text', $content);
//                    message($message['FromUserName'], 'image', 'iVNa-Daw9h5An5r9eWd0Lko9Htb2gV5oLLf4mGGiD2k');
                    break;
            }
        });

        //菜单
//        $this->button();

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
                "name" => "绿叶事业",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "热文文章",
                        "url"  => "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxe910075ca3b12399&redirect_uri=http://btl.yxcxin.com&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect"
                    ]
                ]
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
                }
                $context = "关注欢迎语";
                message($FromUserName, 'text', $context);
                break;
            //取消关注公众号
            case 'unsubscribe':
                User::where('openid', $FromUserName)->update(['subscribe' => 0]);
                break;
            case 'CLICK':
//                return new Image('AD_Lic41HecTBFHSKicKQrwdBglTPvJXd6uEM6A8kdk');
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
    public function checkUser( $FromUserName, $eventkey )
    {
        $user = User::where('openid', $FromUserName)->first();
        if($user) {
            if($user->id !== $eventkey && $user->extension_id == 0 && $user->type == 0) {
                $this->relation($user, $eventkey);
            } else {
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
    public function register($FromUserName, $eventkey)
    {
        $user = $this->app->user->get($FromUserName);
        $data = [
            'wc_nickname' => $user[ 'nickname' ],
            'head' => $user[ 'headimgurl' ],
            'openid' => $user[ 'openid' ],
            'subscribe' => $user[ 'subscribe' ],
            'subscribe_at' => now()->toDateTimeString(),
            'sex' => $user[ 'sex' ]
        ];
        $puser = User::find($eventkey);
        if($puser) {
            $data[ 'superior' ] = $puser->id;
            $data[ 'superior_up' ] = $puser->superior;
            $data[ 'extension_at' ] = now()->toDateTimeString();
            $data[ 'extension_type' ] = '推广二维码';
        }
        //保存用户
        return User::create($data);
    }

    /**
     * 关联账户关系
     * @param $user
     * @param $eventkey
     */
    public function relation( $user, $eventkey )
    {
        $pinfo = User::find($eventkey);
        //当用户本来没有推广用户和经销商的时候
        $user->superior = $pinfo->id;
        $user->superior_up = $pinfo->superior_up;
        $user->extension_at = now()->toDateTimeString();
        $user->extension_type = '推广二维码';
        $user->save();
    }
}
