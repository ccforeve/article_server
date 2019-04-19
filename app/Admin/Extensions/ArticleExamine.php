<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26 0026
 * Time: 上午 10:00
 */

namespace App\Admin\Extensions;


use Encore\Admin\Admin;

class ArticleExamine
{
    protected $id;

    public function __construct( $id )
    {
        $this->id = $id;
    }

    protected function script()
    {
        return <<<SCRIPT
$('button[name=examine]').click(function() {
    let url = $(this).attr('data-url');
    Swal.fire({
      title: '确定通过审核吗?',
      text: "审核通过后将不可撤销",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: '是，审核通过',
      cancelButtonText: '否'
    }).then((result) => {
      if (result.value) {
          $.get(url, function(res) {
            Swal.fire(
              '审核成功'
            )
            $.pjax.reload('#pjax-container');
          })
      }
    })
});
SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<button type='button' name='examine' class='btn btn-primary btn-sm' data-url='" . route('extension_article.examine', $this->id) . "'>审核通过</button>";
    }

    public function __toString()
    {
        return $this->render();
    }
}
