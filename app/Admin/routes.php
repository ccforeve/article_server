<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {
    //首页
    $router->get('/', 'HomeController@index');

    //用户列表
    $router->resource('users', 'UsersController');

    //提现
    $router->resource('cashes', 'CashesController')->only('index');
    //完成提现
    $router->get('cash/operation/{id}', 'CashesController@cashOperation')->name('cash.operation');

    //美图分类
    $router->resource('poster_type', 'PosterTypeController');

    //打卡鼓励语
    $router->resource('punch_comment', 'PunchCommentController');

    //所属类型
    $router->get('poster/type', 'PosterController@getType');
    //美图
    $router->resource('posters', 'PosterController');

    //文章列表
    $router->resource('articles', 'ArticlesController');

    //审核好文章
    $router->post('extension_article/examine/{article}', 'ExtensionArticleController@examine')->name('extension_article.examine');
    //好文章
    $router->resource('extension_article', 'ExtensionArticleController')->only('index', 'show');

    //订单
    $router->resource('orders', 'OrderController')->only('index');
    //退款
    $router->post('order/{order}/refund', 'OrderController@refund')->name('admin.order_refund');

    //会员价格
    $router->resource('payments', 'PaymentController');

    //投诉
    $router->resource('complaint', 'ComplaintController');

    //数据报表
    $router->get('report_index', 'ReportController@index');

    //公众号关键词
    $router->resource('key_word', 'KeyWordController');

    //自定义关键词回复
    $router->resource('key_word_custom', 'KeyWordCustomController');

    //网站报表
    $router->get('report', 'ReportController@index');

    //定时推送消息
    $router->resource('schedules', 'SchedulesController');

    //给未支付用户留言
    $router->post('messages/{user_id}', 'MessagesController@store')->name('admin.order_message');
});
