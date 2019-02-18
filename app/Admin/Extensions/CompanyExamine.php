<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26 0026
 * Time: 上午 10:00
 */

namespace App\Admin\Extensions;


use Encore\Admin\Admin;

class CompanyExamine
{
    protected $id;

    public function __construct( $id )
    {
        $this->id = $id;
    }

    protected function script()
    {
        return <<<SCRIPT

        $('.examine').on('click', function () {
            var url=$(this).data('url');
            swal({ 
              title: '确定审核吗？', 
              text: '审核后将无法恢复！', 
              type: 'warning',
              showCancelButton: true, 
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: '审核通过！', 
              cancelButtonText: '审核不通过！'
            }).then(function(dismiss){
                if(dismiss.value == true) {
                    $.get(url+"?examine=1", function(ret) {
                        swal(
                            '审核！',
                            '审核通过。',
                            'success'
                        );
                    })
                } else if(dismiss.dismiss != 'cancel') {
                    $.get(url+"?examine=2", function(ret) {
                        swal(
                            '审核！',
                            '审核不通过。',
                            'danger'
                        );
                    })
                }
                $.pjax.reload('#pjax-container');
            });
        });
 
SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<a class='btn btn-xs btn-info fa fa-check examine' data-url='" . route('company.examine', $this->id) . "'>审核</a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}