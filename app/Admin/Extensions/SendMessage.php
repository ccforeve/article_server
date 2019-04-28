<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26 0026
 * Time: 上午 10:00
 */

namespace App\Admin\Extensions;


use Encore\Admin\Admin;
use Encore\Admin\Widgets\Form;

class SendMessage
{
    protected $id;

    public function __construct( $id )
    {
        $this->id = $id;
    }

    protected function script()
    {
        return <<<SCRIPT
        $('.send-message').click(function() {
            $(this).next().modal('show')
        })
        $("form[data-form=send-message]").on("submit",function(event){
            event.preventDefault();
            var openid = $(this).find('select[name=openid]').val(),
                url = $(this).attr('action') + '?openid=' + openid
            $.get(url, function(ret) {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $.pjax.reload('#pjax-container');
                toastr.success(ret.message);
            })
        })
SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        $form = new Form();
        $form->attribute('data-form', 'send-message');
        $form->action(route('admin.schedule_send', $this->id));
        $form->disablePjax();
        $form->select('openid', '接收用户')->options(['oWwjo1QUYuZiH6eRqvi-DImSs440' => '李源源', 'oWwjo1VVjsqiLicjmHtOBZR72xgY' => '钟金春', 'oWwjo1VzxOpUzaQJIOQ6SbJ0ArT0' => '万玉亮'])->default('oWwjo1QUYuZiH6eRqvi-DImSs440');
        return <<<HTML
<!-- Button trigger modal -->
<button type="button" class="btn btn-success btn-sm send-message">
  发送查看
</button>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">将发送消息到用户微信</h4>
      </div>
      <div class="modal-body">
        {$form->render()}
      </div>
    </div>
  </div>
</div>
HTML;
    }

    public function __toString()
    {
        return $this->render();
    }
}
