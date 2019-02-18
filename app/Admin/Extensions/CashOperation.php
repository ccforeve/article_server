<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26 0026
 * Time: 上午 10:00
 */

namespace App\Admin\Extensions;


use Encore\Admin\Admin;

class CashOperation
{
    protected $id;

    public function __construct( $id )
    {
        $this->id = $id;
    }

    protected function script()
    {
        return <<<SCRIPT

        $('.cash-operation').on('click', function () {
            var url=$(this).data('url');
            swal({ 
              title: '确定完成提现吗？', 
              text: '确定后将无法恢复！', 
              type: 'warning',
              showCancelButton: true, 
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: '提现完成！', 
              cancelButtonText: '取消'
            }).then(function(dismiss){
                if(dismiss.value == true) {
                    $.get(url, function(ret) {
                        swal(
                            '提现！',
                            '提现完成。',
                            'success'
                        );
                    })
                    $.pjax.reload('#pjax-container');
                }
            });
        });
 
SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<a class='btn btn-xs btn-info fa fa-check cash-operation' data-url='" . route('cash.operation', $this->id) . "'></a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}