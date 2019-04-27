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

class Refund
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
        $("form[data-form=refund]").on("submit",function(event){
            event.preventDefault();
            var refund_fee = $(this).find('input[name=refund_fee]').val(),
                order_fee = $(this).find('input[name=order_fee]').val(),
                refund_remark = $(this).find('input[name=refund_remark]').val(),
                url = $(this).attr('action')
            if(refund_fee <= 0 || refund_fee > order_fee) {
                Swal.fire({
                  type: 'error',
                  title: '退款金额不可小于0等于0或大于订单金额',
                })
                return
            }
            $.post(url, {refund_fee: refund_fee, refund_remark: refund_remark, _token: LA.token}, function(ret) {
                if(ret.code == 200) {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                    $.pjax.reload('#pjax-container');
                    toastr.success(ret.message);
                } else {
                    Swal.fire({
                        type: 'error',
                        title: ret.message,
                        text: '错误码' + ret.code
                    })
                }
            })
        })
SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        $form = new Form();
        $form->attribute('data-form', 'refund');
        $form->action(route('admin.order_refund', $this->order->id));
        $form->disablePjax();
        $form->display('price', '订单金额')->default($this->order->price);
        $form->text('refund_remark', '退款原因');
        $form->text('refund_fee', '退款金额')->default($this->order->price);
        return <<<HTML
<!-- Button trigger modal -->
<button type="button" class="btn btn-danger btn-sm refund">
  退款
</button>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">退款（退款人：{$this->order->user->nickname}）</h4>
        <input id="order_fee" type="hidden" value="{$this->order->price}">
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
