<?php

namespace App\Admin\Controllers;

use App\Models\KeyWord;
use App\Http\Controllers\Controller;
use App\Models\KeyWordCustom;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class KeyWordController extends Controller
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
            ->header('公众号关键词')
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
            ->header('公众号关键词')
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
            ->header('公众号关键词')
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
            ->header('公众号关键词')
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
        $grid = new Grid(new KeyWord);

        $grid->id('Id');
        $grid->name('关键词');
        $grid->cmd('规则名');
        $grid->type('处理类型')->using([0 => '等于', 1 => '前置', 2 => '中间', 3 => '后置', 4 => '正则', 5 => '包含多个']);
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
        $show = new Show(KeyWord::findOrFail($id));

        $show->id('Id');
        $show->name('关键词');
        $show->cmd('规则名');
        $show->type('处理类型');
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
        $form = new Form(new KeyWord);

        $form->text('name', '关键词');
        $form->text('cmd', '规则名');
        $form->select('type', '处理类型')->options([0 => '等于', 1 => '前置', 2 => '中间', 3 => '后置', 4 => '正则', 5 => '包含多个']);
        $form->select('custom_id', '返回自定义')->options(KeyWordCustom::query()->pluck('name', 'id'));

        return $form;
    }
}
