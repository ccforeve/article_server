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

class PresaleContact
{
    protected $id;

    public function __construct( $id )
    {
        $this->id = $id;
    }

    protected function script()
    {
        return <<<SCRIPT
        $('.contact').click(function() {
            var url = $(this).attr('data-url')
            console.log(url)
            $.get(url, function(ret) {
                $.pjax.reload('#pjax-container');
                toastr.success('操作成功');
            })
        })
        
SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<button type='button' name='contact' class='btn btn-primary btn-sm contact' data-url='" . route('presale.contact', $this->id) . "'>电联</button>";
    }

    public function __toString()
    {
        return $this->render();
    }
}
