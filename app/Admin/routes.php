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

    $router->resource('articles', 'ArticlesController');

    //审核好文章
    $router->post('good_article/examine/{id}', 'GoodArticleController@examine')->name('good_article.examine');
    //好文章
    $router->resource('good_article', 'GoodArticleController');

    //订单
    $router->resource('order', 'OrderController')->only('index');

    //投诉
    $router->resource('complaint', 'ComplaintController');

    //数据报表
    $router->get('report_index', 'ReportController@index');

    //公众号关键词
    $router->resource('key_word', 'KeyWordController');

    //自定义关键词回复
    $router->resource('key_word_custom', 'KeyWordCustomController');
});
