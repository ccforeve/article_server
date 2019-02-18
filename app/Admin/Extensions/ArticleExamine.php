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

        $('.examine').on('click', function () {
            var url=$(this).data('url');
            swal({
              title: '提交审核后的好文章链接',
              input: 'textarea',
              showCancelButton: true,
              confirmButtonText: '提交',
              cancelButtonText: '取消',
              showLoaderOnConfirm: true,
              preConfirm: function(article_url) {
                 $.post(url, {article_url:article_url,_token:LA.token}, function(ret) {
                     swal(
                        '审核！',
                        '审核完成！',
                        'success'
                     )
                     $.pjax.reload('#pjax-container');
                 })
              },
              allowOutsideClick: false
            }).then(function(email) {
              //取消
            })
        });
 
SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<a class='btn btn-xs btn-info fa fa-check examine' data-url='" . route('good_article.examine', $this->id) . "'></a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}