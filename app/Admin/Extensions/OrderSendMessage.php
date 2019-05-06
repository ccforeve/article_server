<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/8 0008
 * Time: 下午 3:43
 */

namespace App\Admin\Extensions;

use App\Models\Order;
use Encore\Admin\Admin;
use Encore\Admin\Widgets\Form;

class OrderSendMessage
{
    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    protected function script()
    {
        return <<<SCRIPT
        $('.refund').click(function() {
            $(this).next().modal('show')
        })
        $("form[data-form=message]").on("submit",function(event){
            event.preventDefault();
            var submit_user_id = $(this).find('select[name=submit_user_id]').val(),
                type = $(this).find('select[name=type]').val(),
                region = $(this).find('input[name=region]').val(),
                name = $(this).find('input[name=name]').val(),
                gender = $(this).find('select[name=gender]').val(),
                phone = $(this).find('input[name=phone]').val(),
                message = $(this).find('textarea[name=message]').val(),
                url = $(this).attr('action')
            let data = {
                submit_user_id: submit_user_id, 
                type: type, 
                region: region, 
                name, name, 
                gender: gender, 
                phone: phone, 
                message: message, 
                _token: LA.token
            }
            $.post(url, data, function(ret) {
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
        $form->attribute('data-form', 'message');
        $form->action(route('admin.order_message', $this->order->id));
        $form->disablePjax();
        $form->select('submit_user_id', '留言人')->options([20 => '李源源（徐启隆）', 38 => '万玉亮（D.m）', 5057 => '李源源（ABC健康成长）', 5074 => "李源源（健康有道（雷淑霞））"])->default(20);
        $form->select('type', '咨询问题')->options(['咨询健康问题' => '咨询健康问题', '了解加盟事业' => '了解加盟事业', '其他' => '其他'])->default('咨询健康问题');
        $form->text('region', '工作地区')->default(array_random(['深圳', '上海', '无锡', '百色']));
        $form->text('name', '姓名');
        $form->select('gender', '性别')->options([1 => '男', 2 => '女'])->default(1);
        $form->text('phone', '手机号');
        $form->textarea('message', '咨询内容');
        return <<<HTML
<!-- Button trigger modal -->
<button type="button" class="btn btn-success btn-sm refund">
  留言
</button>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">留言（被留言人：{$this->order->user->nickname}）</h4>
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
