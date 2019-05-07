<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\KeyWordCustom;
use App\Models\WechatTemplate;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class WechatTemplateController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('模板消息')
            ->description('列表')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('模板消息')
            ->description('查看')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('模板消息')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('模板消息')
            ->description('添加')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WechatTemplate);

        $grid->id('Id');
        $grid->name('模板消息名称');
        $grid->template_id('模板id');
        $grid->url('跳转链接');
        $grid->first('头部');
        $grid->keyword('内容');
        $grid->remark('底部');
        $grid->created_at('添加时间');
        $grid->updated_at('更新时间');

        $grid->disableExport();
        $grid->perPages([10, 20]);

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WechatTemplate::findOrFail($id));

        $show->id('Id');
        $show->created_at('添加时间');
        $show->updated_at('更新时间');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WechatTemplate);

        $form->text('name', '模板消息名称');
        $form->text('template_id', '模板id')->rules('required', ['不能为空']);
        $form->text('url', '跳转链接');
        $form->embeds('first', '模板消息头部', function ($form) {
            $form->text('message', '内容样板');
            $form->color('color', '颜色');
        });
        $form->table('keyword', '中间内容', function ($table) {
            $table->text('key', '说明');
            $table->text('message', '内容');
            $table->color('color', '颜色');
        });
        $form->embeds('remark', '模板消息底部', function ($form) {
            $form->text('message', '内容样板');
            $form->color('color', '颜色');
        });

        return $form;
    }
}
