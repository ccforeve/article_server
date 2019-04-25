<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\Index',
    'middleware' => ['serializer:array', 'bindings'],
], function($api) {
    //备用路由
    Route::fallback(function(){
        return response()->json(['message' => '接口链接不正确'], 404);
    });

    //测试
    $api->get('test', 'TestsController@test');

//    $api->get('wechat_public_qrcode', 'UsersController@getWechatQrcode');
    //微信授权登录
    $api->get('user/login', 'UsersController@login');
    //微信开发配置
    $api->any('wechat', 'WechatController@index');
    //微信公众号菜单
    $api->get('wechat/create_button', 'WechatController@button');
    $api->get('wechat/upload_image', 'WechatController@uploadImage');
    //微信jssdk配置
    $api->get('wechat/config', 'UsersController@config');
    //滚动信息
    $api->get('orders', 'PayController@orders');
    //微信支付回调
    $api->any('wechat_out_trade', 'PayController@outTradeNo')->name('api.wechat_out_trade');
    //支付宝支付
    $api->get('alipay/pay/{order}', 'PayController@alipay');
    //支付宝支付回调
    $api->any('alipay/notify', 'PayController@alipayNotify')->name('api.alipay_notify');
    //添加产品接口
    $api->post('products', 'ProductsController@store');
    //更新预售产品
    $api->get('products/update', 'ProductsController@updateProducts');
    //添加产品分类接口
    $api->post('product_categories', 'ProductCategoriesController@store');
    // 需要 token 验证的接口
    $api->group(['middleware' => 'user.oauth'], function($api) {
        $api->group([ 'prefix' => 'user' ], function ( $api ) {
            //获取用户资料
            $api->get('/', 'UsersController@me');
            //编辑用户资料
            $api->patch('/', 'UsersController@update');
            //个人中心
            $api->get('center', 'UsersController@center');
            //获取用户头像和二维码的base64文件
            $api->get('wechat_qrcode_and_user_avatar', 'UsersController@getWechatQrcodeAndUserAvatar');
            //获取带用户id的微信二维码
            $api->get('wechat_qrcode/{user_id}', 'UsersController@getWechatQrcode');
        });
        //文章分类列表
        $api->get('article_categories', 'ArticleCategoriesController@list');
        //文章列表
        $api->get('articles', 'ArticlesController@list');
        //文章详情
        $api->get('articles/{article_id}', 'ArticlesController@show')->where(['article_id' => '[0-9]+']);
        //成为我的文章
        $api->get('articles/become_my', 'UserArticlesController@becomeMyArticle');
        //推荐好文章
        $api->post('articles/extension', 'ArticlesController@extension');
        //海报分类列表
        $api->get('poster/categories', 'PosterCategoriesController@list');
        $api->group(['prefix' => 'posters'], function ($api) {
            //分类的海报
            $api->get('cate/{category_id}', 'PostersController@catePoster');
            //品牌的海报
            $api->get('brand/{category_id}', 'PostersController@brandPoster');
            //海报详情
            $api->get('{poster}', 'PostersController@show')->where(['poster' => '[0-9]+']);
            //获取打卡海报
            $api->get('punch', 'PostersController@getPunchPoster');
            //随机获取海报
            $api->get('random/{count}', 'PostersController@random');
            //发送海报到微信公众号
            $api->post('send_poster_wechat', 'PostersController@sendPoster');
        });
        //用户文章列表
        $api->get('user/{user_id}/articles', 'UserArticlesController@list');
        //我的文章详情页
        $api->get('user_articles/{article_id}/{share_id?}', 'UserArticlesController@show')->where(['share_id' => '[0-9]+']);
        //分享文章
        $api->get('user_articles/{user_article}/share', 'UserArticlesController@shareSuccess');
        //上传图片
        $api->post('upload/image', 'UploadController@upload');
        $api->group(['prefix' => 'messages'], function ($api) {
            //留言
            $api->get('/', 'MessagesController@list');
            //留言详情
            $api->get('{message}', 'MessagesController@show');
            //新增留言
            $api->post('/', 'MessagesController@story');
            //家庭留言总数
            $api->get('family/total', 'MessagesController@familyMessageCount');
        });
        $api->group(['prefix' => 'footprints'], function ($api) {
            //实时更新阅读文章时间
            $api->patch('update_read_time/{footprint}', 'FootprintsController@updateReadTime');
            //谁查看我的文章记录（阅读记录）
            $api->get('read', 'FootprintsController@read');
            //谁查看我的文章记录（分享记录）
            $api->get('share', 'FootprintsController@share');
            //找到用户
            $api->get('find_visitor/{user_id}', 'FootprintsController@findVisitor');
        });
        //会员价格列表
        $api->get('payments', 'PaymentsController@list');
        //支付
        $api->post('pay/{payment}', 'PayController@addOrder');
        $api->group(['prefix' => 'profit'], function ($api) {
            //推广中心
            $api->get('/', 'ProfitsController@index');
            //普通用户推广详情
            $api->get('normal', 'ProfitsController@normal');
            //经销商推广详情
            $api->get('dealer', 'ProfitsController@dealer');
            //提现记录
            $api->get('withdraw_cash_list', 'ProfitsController@withdrawCashList');
            //提现操作
            $api->post('withdraw_cash', 'ProfitsController@withdrawCash');
            //推广的用户列表
            $api->get('extension_users', 'ProfitsController@extensionUsers');
            //推广成功的订单列表
            $api->get('extension_orders', 'ProfitsController@extensionOrder');
        });
        $api->group(['prefix' => 'visitor'], function ($api) {
            //访客记录
            $api->get('/', 'VisitorsController@index');
            //今日浏览和准客户
            $api->get('read_custom', 'FootprintsController@readAndCustom');
            //谁还看了
            $api->get('user_article_show/{user_article}', 'VisitorsController@userArticleShow');
            //他还看了
            $api->get('also_read/{user_id}', 'VisitorsController@alsoRead');
            //准客户列表
            $api->get('prospect', 'VisitorsController@prospect');
            //查看是否有新的访客
            $api->get('if_new_footprint', 'VisitorsController@ifHasVisitor');
            //更新访客足迹状态，页面红点去掉
            $api->get('update_state', 'VisitorsController@updateNewState');
        });
        $api->group(['prefix' => 'punch'], function ($api) {
            //打卡页面数据
            $api->get('/', 'PunchesController@index');
            //打卡操作
            $api->get('store', 'PunchesController@punchCard');
            //获取省份打卡第一名数据
            $api->get('province', 'PunchesController@getProvincePunch');
            //获取全国前100打卡数据
            $api->get('top_hundred', 'PunchesController@getTotal');
            //获取当前用户打卡记录
            $api->get('current_user', 'PunchesController@getCurrentUserPunch');
        });
        $api->get('products/search', 'ProductsController@searchList');
    });
});
