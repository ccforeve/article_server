<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/8 0008
 * Time: 下午 2:50
 */
/**
 * @title 推送文本、图片消息
 * @param $app
 * @param $FromUserName
 * @param $type
 * @param $context
 */
function message($FromUserName,$type,$context)
{
    info($FromUserName);
    info($type);
    info($context);
    switch($type) {
        case 'text':
            //推送推广成功消息（客服消息）
            $message = new \EasyWeChat\Kernel\Messages\Text($context);
            break;
        case 'image':
            //推送推广图片消息（客服消息）
            $message = new \EasyWeChat\Kernel\Messages\Image($context);
            break;
//        case 'article':
//            $items = [
//                new \EasyWeChat\Kernel\Messages\NewsItem([
//                    'title'       => $context['title'],
//                    'description' => str_limit(strip_tags($context['details']), 70),
//                    'url'         => route('article_details', $context['id']),
//                    'image'       => $context['pic'],
//                ]),
//            ];
//            $message = new \EasyWeChat\Kernel\Messages\News($items);
//            break;
    }
    try {
        $app = \EasyWeChat::officialAccount();
        $app->customer_service->message($message)->to($FromUserName)->send();
    } catch (Exception $exception) {
        info("发送类型：{$type}；错误：{$exception->getMessage()}。发送用户openid：{$FromUserName}");
    }
}


/**
 * @title 推送模板消息
 * @param $openid
 * @param array $data 模板内容（数组）
 * @param $template_id 模板id
 * @param $url 跳转链接
 * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
 */
function template_message($openid,array $data,$template_id,$url)
{
    $app = \EasyWeChat\Factory::officialAccount(config('wechat.defaults'));
    //推送模板消息
    $app->template_message->send([
        'touser' => $openid,
        'template_id' => $template_id,
        'url' => $url,
        'data' => $data
    ]);
}

/**
 * @param $img_base64
 * @param $file_name
 * @return array
 */
function base64ChangeImg($img_base64,$file_name)
{
    $base64_image_content = $img_base64;
    $daytime = date('Y');

    //匹配出图片的格式
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
        $type = $result[2];
        $new_file = config('app.image_real_path')."uploads/$file_name/$daytime/";
        if(!file_exists($new_file))
        {
            //检查是否有该文件夹，如果没有就创建，并给予最高权限
            mkdir($new_file, 0777, true);
        }
        $image_name = time().rand(1000,9999);
        $file = $image_name.'.'.$type;
        if (file_put_contents($new_file.$file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
            return ['state'=>1, 'path'=>"$file_name/$daytime/$file"];
        }else{
            return ['state'=>0];
        }
    }
}

/**
 * 图片转成base64位
 * @param $is_cache     '是否使用缓存'
 * @param $cache_name   '缓存名称'
 * @param $url          '图片链接'
 * @return string
 */
function imgChangeBase64 ($url, $cache_name = '') {
    $base64 = base64_encode(file_get_contents($url));
    if($cache_name) {
        return Cache::remember($cache_name, 60 * 24 * 5, function () use ($base64) {
            return "data:image/jpeg;base64," . $base64;
        });
    } else {
        return "data:image/jpeg;base64," . $base64;
    }
}
