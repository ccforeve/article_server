<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/21 0021
 * Time: 上午 8:52
 */

namespace App\Swoole\Services;


use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;

class WebSocketService implements WebSocketHandlerInterface
{
// 声明没有参数的构造函数
    public function __construct()
    {
    }
    public function onOpen(\swoole_websocket_server $server, \swoole_http_request $request)
    {
        $fds = \Cache::get('fds') ?? [];
        array_push($fds, $request->fd);
        \Cache::put('fds', $fds, 600);
        // 在触发onOpen事件之前Laravel的生命周期已经完结，所以Laravel的Request是可读的，Session是可读写的
        $server->push($request->fd, implode('|', $fds));
//        $server->push($request->fd, 'Welcome to LaravelS');
        // throw new \Exception('an exception');// 此时抛出的异常上层会忽略，并记录到Swoole日志，需要开发者try/catch捕获处理
    }
    public function onMessage(\swoole_websocket_server $server, \swoole_websocket_frame $frame)
    {
        $server->push($frame->fd, $frame->data . '|' . date('Y-m-d H:i:s'));
        // throw new \Exception('an exception');// 此时抛出的异常上层会忽略，并记录到Swoole日志，需要开发者try/catch捕获处理
    }
    public function onClose(\swoole_websocket_server $server, $fd, $reactorId)
    {
        // throw new \Exception('an exception');// 此时抛出的异常上层会忽略，并记录到Swoole日志，需要开发者try/catch捕获处理
    }
}
