<?php


namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class UserMessage extends AbstractTool
{
    protected function script()
    {
        $url = Request::fullUrlWithQuery(['message' => '_message_']);

        return <<<EOT

$('input:radio.user-message').change(function () {

    var url = "$url".replace('_message_', $(this).val());

    $.pjax({container:'#pjax-container', url: url });

});

EOT;
    }

    public function render()
    {
        Admin::script($this->script());

        $options = [
            'all'   => '全部用户',
            1     => '可留言用户'
        ];

        return view('admin.tools.user_message', compact('options'));
    }
}
