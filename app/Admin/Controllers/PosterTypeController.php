<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PosterCategory;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class PosterTypeController extends Controller
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
            ->header('美图分类')
            ->description('分类')
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
            ->header('分类详情')
            ->description('详情')
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
            ->header('编辑分类')
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
            ->header('创建分类')
            ->description('创建')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PosterCategory());

        $grid->model()->latest('id');

        $grid->id('Id');
        $grid->name('名称')->editable();
        $grid->sort('排序')->editable();
        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');

        $grid->disableRowSelector();
        $grid->disableExport();
        $grid->disableFilter();

        $grid->actions(function ($action) {
            $action->disableEdit();
            $action->disableView();
        });

        $grid->perPages([15, 20]);

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
        $show = new Show(PosterCategory::findOrFail($id));

        $show->id('Id');
        $show->name('名称');
        $show->sort('排序');
        $show->created_at('创建时间');
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
        $form = new Form(new PosterCategory);

        $form->text('name', '名称');
        $form->number('sort', '排序');

        return $form;
    }
}
