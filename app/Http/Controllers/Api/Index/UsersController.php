<?php

namespace App\Http\Controllers\Api\Index;

use App\Http\Controllers\Api\Controller;
use App\Models\User;
use App\Services\UserService;
use App\Transformers\UserTransformer;
use Auth;
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
     * 登录
     * @param Request $request
     * @param Application $app
     * @return \Dingo\Api\Http\Response
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function login( Request $request, Application $app )
    {
        $driver = Socialite::driver('weixin');
        $response = $driver->getAccessTokenResponse($request->code);
        $driver->setOpenId($response['openid']);
        $user_info = $app->user->get($response['openid']);
        $subscribe = true;
        if(!$user_info['subscribe']) {
            $oauthUser = $driver->userFromToken($response['access_token']);
            $user_info = collect($oauthUser->user);
            $subscribe = false;
        }
        $user = User::query()->where('openid', $user_info['openid'])->first();
        if(!$user) {
            $user = User::create([
                'openid'    => $user_info['openid'],
                'nickname'  => $user_info['nickname'],
                'sex'       => $user_info['sex'],
                'avatar'    => $user_info['headimgurl'],
                'subscribe'    => $subscribe ? 1 : 0
            ]);
        }

        return $this->response->item($user, new UserTransformer())
            ->setMeta([
                'msg' => '授权成功',
                'access_token'  => 'Bearer ' . Auth::guard('api')->login($user),
                'token_type'    => 'Bearer',
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

    public function getWechatQrcode(Application $app)
    {
        $user = $this->user();
        $result = $app->qrcode->temporary($user->id, 6 * 24 * 3600);
        $url = $app->qrcode->url($result['ticket']);
        $qrcode = imgChangeBase64($url, 'qrcode_' . $user->openid);
        $avatar = imgChangeBase64($user->avatar, 'avatar' . $user->openid);
//        $content = config('app.url'). "/image/qrcode.jpg";

        return $this->response->array([
            'code' => 200,
            'qrcode' => $qrcode,
            'avatar' => $avatar
        ]);
    }
}
