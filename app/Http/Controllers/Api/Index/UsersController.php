<?php

namespace App\Http\Controllers\Api\Index;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\MiniprogramLoginRequest;
use App\Jobs\UploadAvatar;
use App\Models\Article;
use App\Models\Presale;
use App\Models\User;
use App\Models\UserArticle;
use App\Services\UserService;
use App\Transformers\UserTransformer;
use Auth;
use Carbon\Carbon;
use EasyWeChat\OfficialAccount\Application;
use Encore\Admin\Admin;
use Encore\Admin\Auth\Database\Administrator;
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

    /**
     * 微信公众号登录
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function login( Request $request, Application $app )
    {
        $driver = Socialite::driver('weixin');
        $response = $driver->getAccessTokenResponse($request->code);
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
                'subscribe' => $subscribe ? 1 : 0,
                'unionid'   => $user_info['unionid']
            ]);
            dispatch(new UploadAvatar($user->id, $user->avatar));
        } else {
            $user->unionid = $user_info['unionid'];
            if ($user->isDirty('unionid')) {
                $user->save();
            }
        }

        return $this->response->item($user, new UserTransformer())
            ->setMeta([
                'msg'          => '授权成功',
                'access_token' => 'Bearer ' . Auth::guard('api')->login($user),
                'token_type'   => 'Bearer',
            ])->statusCode(201);
    }

    /**
     * 小程序登录
     * @param \EasyWeChat\MiniProgram\Application $app
     * @param Request $request
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function miniprogramLogin( \EasyWeChat\MiniProgram\Application $app, Request $request )
    {
        $session = $app->auth->session($request->code);
        if (isset($session['unionid'])) {
            $user = User::query()->where('unionid', $session['unionid'])->first();
            if ($user) {
                return $this->response->array([
                    'code' =>200,
                    'user_id' => $user->id,
                    'openid' => $session['openid'],
                    'message' => '存在该用户'
                ]);
            } else {
//                $userInfo = $request->userInfo;
//                $user = User::create([
//                    'openid'    => $session[ 'unionid' ],
//                    'nickname'  => $userInfo[ 'nickName' ],
//                    'sex'       => $userInfo[ 'gender' ],
//                    'avatar'    => $userInfo[ 'avatarUrl' ],
//                    'unionid'   => $session[ 'unionid' ]
//                ]);
//                dispatch(new UploadAvatar($user->id, $user->avatar));
            }
        }
        return $this->response->array([
            'code' =>401,
            'openid' => $session['openid'],
            'session_key' => $session['session_key'],
            'message' => '不存在该用户'
        ]);
    }

    /**
     * 小程序授权后查找用户
     * @param \EasyWeChat\MiniProgram\Application $app
     * @param MiniprogramLoginRequest $request
     * @return \Dingo\Api\Http\Response|void
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     */
    public function miniprogramAuthorizon( \EasyWeChat\MiniProgram\Application $app, MiniprogramLoginRequest $request )
    {
        $user_info = $app->encryptor->decryptData($request->session_key, $request->iv, $request->encryptedData);
        $user = User::query()->where('openid', $user_info['unionId'])->value('id');
        if (!$user) {
            return $this->response->array([
                'code' =>401,
                'message' => '不存在该用户'
            ]);
        }
        return $this->response->array([
            'code' =>200,
            'user_id' => $user->id,
            'message' => '存在该用户'
        ]);
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
        $admin_id = 0;
        //分配售前客服
        if(isset($request->phone) && !Presale::query()->where('user_id', $user->id)->value('id')) {
            $admin_id = \DB::table('admin_role_users')->where('role_id', 3)->inRandomOrder()->value('user_id');
            Presale::query()->create([
                'admin_id' => $admin_id,
                'user_id' => $user->id
            ]);
        }

        return $this->response->array([
            'code' => 0,
            'message' => '修改成功',
            'data' => ['admin_id' => $admin_id]
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
