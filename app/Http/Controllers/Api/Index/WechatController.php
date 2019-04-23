<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/10 0010
 * Time: 下午 12:27
 */

namespace App\Http\Controllers\Api\Index;

use App\Http\Controllers\Controller;
use App\Http\Traits\WechatNotifiable;
use App\Models\User;
use Carbon\Carbon;
use EasyWeChat\OfficialAccount\Application;

class WechatController extends Controller
{
    use WechatNotifiable;

    protected $app;

    public $last_week;

    public $this_week;

    public $next_week;

    public function __construct( Application $app )
    {
        $this->app = $app;
        $this->last_week = Carbon::parse('last week')->startOfDay();
        $this->this_week = Carbon::parse('this week')->startOfDay();
        $this->next_week = Carbon::parse('next week')->startOfDay();
    }

    public function url( $value )
    {
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxe910075ca3b12399&redirect_uri={$value}&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
    }

    /**
     * 上传素材
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function uploadImage()
    {
        $material = $this->app->material;
        $result = $material->uploadImage(public_path('image/')."study_image.jpg");
        return $result;
    }

    /**
     * 清除缓存
     */
    public function reloadWechatShareConfig()
    {
        \Cache::flush();
    }

    public function index()
    {
        $app = $this->app;
        $app->server->push(function ($message) {
            switch ($message['MsgType']) {
                //收到事件消息
                case 'event':
                    return $this->_event($message);
                    break;
                //收到文字消息
                case 'text':
                    return $this->_text($message['Content']);
                    break;
                case 'voice':
                    return $this->_voice($message['Recognition']);
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
                "name"       => "事业分享",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "分享事业",
                        "url"  => $this->url('http://btl.yxcxin.com')
                    ],
                    [
                        "type" => "view",
                        "name" => "签到打卡",
                        "url"  => $this->url('http://btl.yxcxin.com/punch')
                    ]
                ],
            ],
            [
                "type" => "view",
                "name" => "访客记录",
                "url"  => $this->url('http://btl.yxcxin.com/visitor')
            ],
            [
                "name"       => "服务",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "个人中心",
                        "url"  => $this->url('http://btl.yxcxin.com/user')
                    ],
                    [
                        "type" => "click",
                        "name" => "查询教程",
                        "key"  => "study"
                    ],
                    [
                        "type" => "click",
                        "name" => "客服微信",
                        "key"  => "server"
                    ],
//                    [
//                        "type" => "view",
//                        "name" => "操作指南",
//                        "url"  => $this->url('http://btl.yxcxin.com')
//                    ]
                ],
            ],
        ];
        $app->menu->create($buttons);
    }

    /**
     * 文字事件
     * @param $openid
     * @param $content
     * @return string
     */
    public function _text( $content )
    {
        return $this->searchProduct( $content );
    }

    /**
     * 语音事件
     * @param $recognition
     * @return string
     */
    public function _voice( $recognition )
    {
        return $this->searchProduct( $recognition );
    }

    /**
     * 公众号事件推送分流
     * @param $FromUserName
     * @param $event
     * @param $eventkey
     * @return string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function _event($message)
    {
        $FromUserName = $message['FromUserName'];
        $event = $message['Event'];
        $eventkey = isset($message['EventKey']) ? $message['EventKey'] : null;
        switch ($event) {
            //已关注公众号的
            case 'SCAN':
                if(is_numeric($eventkey)){
                    $this->checkUser($FromUserName, $eventkey);
                }
                break;
            //未关注公众号的
            case 'subscribe':
                //扫推送二维码关注公众号（创建账号）
                if (strpos($eventkey, '_') !== false) {
                    $eventkey = str_replace('qrscene_', '', $eventkey);
                    $user = $this->checkUser($FromUserName, $eventkey);
                } else {
                    $user = $this->checkUser($FromUserName);
                }
                $context = "{$user->nickname}，你好\n\n恭喜你找到事业分享神器\n“事业分享”为你准备了大量的行业文章。\n每天都会持续稳定更新。\n让你可以快速成长，获取专业知识。\n是你健康事业一大利器！\n你可以通过“分享事业”点击进入，分享里面的文章。\n分享到朋友圈和好友群之后，如有人点开你分享的文章浏览，我们会第一时间通知您，让你第一时间和客户取得联系。不遗漏每一位潜在客户！！！\n\n点击👇👇👇“分享事业”开启你互联网健康事业的第一步吧！";
                return $context;
                break;
            //取消关注公众号
            case 'unsubscribe':
                User::where('openid', $FromUserName)->update(['subscribe' => 0]);
                break;
            case 'CLICK':
                if($eventkey == 'study') {
                    message($FromUserName, 'image', 'iVNa-Daw9h5An5r9eWd0Lichp133IAvmP24TFejhHyE');
                } elseif($eventkey == 'server') {
                    message($FromUserName, 'image', 'iVNa-Daw9h5An5r9eWd0LsJW7PzzIT0LvOp-6pcK9Dk');
                }
                return '';
                break;
        }
    }
}
