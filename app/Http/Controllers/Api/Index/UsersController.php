<?php

namespace App\Http\Controllers\Api\Index;

use App\Http\Controllers\Api\Controller;
use App\Jobs\UploadAvatar;
use App\Models\User;
use App\Services\UserService;
use App\Transformers\UserTransformer;
use Auth;
use Carbon\Carbon;
use EasyWeChat\OfficialAccount\Application;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class UsersController extends Controller
{
    /**
     * 用户资料
     * @param Request $request
     * @return \Dingo\Api\Http\Response|mixed
     */
    public function me(Request $request)
    {
        $user = $this->user();
        if($user_id = $request->user_id) {
            $user = User::find($user_id);
        }
        return $this->response->item($user, new UserTransformer());
    }

    /**
     * 获取微信sdk配置
     * @param Application $app
     * @param Request $request
     * @return array|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function config( Application $app, Request $request )
    {
        $user = $this->user();
        if($user) {
            $config = json_decode($app->jssdk->buildConfig([ 'chooseWXPay', 'onMenuShareTimeline', 'onMenuShareAppMessage' ], urldecode($request->url)), true);
            $user = collect($user)->put('is_member', ( Carbon::parse($user->member_lock_at)->gt(now()) ? 1 : 0 ));
            return $this->response->array([ 'config' => $config, 'user' => $user ]);
        }
    }

    public function testLogin()
    {
        $user = User::query()->find(50);
        return $this->response->item($user, new UserTransformer())
            ->setMeta([
                'msg'          => '授权成功',
                'access_token' => 'Bearer ' . Auth::guard('api')->login($user),
                'token_type'   => 'Bearer',
            ])->statusCode(201);
    }

    /**
     * 登录
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function login( Request $request, Application $app )
    {
        $driver = Socialite::driver('weixin');
        $response = $driver->getAccessTokenResponse($request->code);
        if(!isset($response['openid'])) {
            info('微信登录错误', [$response]);
            return null;
        }
        $driver->setOpenId($response[ 'openid' ]);
        $user_info = $app->user->get($response[ 'openid' ]);
        $subscribe = true;
        if ( !$user_info[ 'subscribe' ] ) {
            $oauthUser = $driver->userFromToken($response[ 'access_token' ]);
            $user_info = collect($oauthUser->user);
            $subscribe = false;
        }
        $user = User::query()->where('openid', $user_info[ 'openid' ])->first();
        if ( !$user ) {
            $user = User::create([
                'openid'    => $user_info[ 'openid' ],
                'nickname'  => $user_info[ 'nickname' ],
                'sex'       => $user_info[ 'sex' ],
                'avatar'    => $user_info[ 'headimgurl' ],
                'subscribe' => $subscribe ? 1 : 0
            ]);
            dispatch(new UploadAvatar($user->id, $user->avatar));
        }

        return $this->response->item($user, new UserTransformer())
            ->setMeta([
                'msg'          => '授权成功',
                'access_token' => 'Bearer ' . Auth::guard('api')->login($user),
                'token_type'   => 'Bearer',
            ])->statusCode(201);
    }

    /**
     * 个人中心
     * @param UserService $service
     * @return array
     */
    public function center(UserService $service)
    {
        $user = $this->user();
        $count = $service->center($user->id);

        return $count;
    }

    /**
     * 更新用户信息
     * @param Request $request
     * @return mixed
     */
    public function update( Request $request )
    {
        $user = $this->user();
        $data = $request->all();
        User::query()->where('id', $user->id)->update($data);

        return $this->response->array([
            'code' => 0,
            'message' => '修改成功'
        ]);
    }

    /**
     * 获取用户头像和微信二维码的base64
     * @param Application $app
     * @return mixed
     */
    public function getWechatQrcodeAndUserAvatar(Application $app)
    {
        $user = $this->user();
        $result = $app->qrcode->temporary($user->id, 6 * 24 * 3600);
        $url = $app->qrcode->url($result['ticket']);
        $qrcode = imgChangeBase64($url, 'qrcode_' . $user->openid);
        $avatar = imgChangeBase64($user->avatar, 'avatar' . $user->openid);

        return $this->response->array([
            'qrcode' => $qrcode,
            'avatar' => $avatar
        ]);
    }

    /**
     * 获取带用户id的微信二维码
     * @param Application $app
     * @return mixed
     */
    public function getWechatQrcode( Application $app, $user_id )
    {
        $result = $app->qrcode->temporary($user_id, 6 * 24 * 3600);
        $qrcode_url = $app->qrcode->url($result['ticket']);

        return $this->response()->array(['qrcode' => $qrcode_url]);
    }
}
