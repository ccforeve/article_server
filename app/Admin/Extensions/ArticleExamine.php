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
        $('.examine').click(function() {
            $(this).next().modal('show')
        })
        $("form[data-form=examine]").on("submit",function(event){
            event.preventDefault();
            var article_id = $(this).find('input[name=article_id]').val(),
                url = $(this).attr('action')
            $.post(url, {article_id: article_id, _token: LA.token}, function(ret) {
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
        $form->attribute('data-form', 'examine');
        $form->action(route('extension_article.examine', $this->id));
        $form->disablePjax();
        $form->text('article_id', '文章id')->help('必须文章id!!!');
        return <<<HTML
<!-- Button trigger modal -->
<button type="button" class="btn btn-primary btn-sm examine">
  审核通过
</button>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        {$form->render()}
      </div>
    </div>
  </div>
</div>
HTML;
//        return "<button type='button' name='examine' class='btn btn-primary btn-sm' data-url='" . route('extension_article.examine', $this->id) . "'>审核通过</button>";
    }

    public function __toString()
    {
        return $this->render();
    }
}
