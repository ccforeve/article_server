<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PosterCategory;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class AdminsController extends Controller
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
            ->header('售前员工')
            ->description('列表')
            ->body($this->grid());
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
            ->header('售前员工')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new \Encore\Admin\Auth\Database\Administrator());

        $grid->model()->whereIn('id', [2, 5, 6]);

        $grid->id('Id');
        $grid->name('姓名');
        $grid->qrcode('二维码')->image('http://cdn.yxcxin.com/uploads', 100, 100);

        $grid->disableCreateButton();
        $grid->disableRowSelector();
        $grid->disableExport();
        $grid->disableFilter();

        $grid->actions(function ($action) {
            $action->disableView();
            $action->disableDelete();
        });

        $grid->perPages([15, 20]);

        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new \Encore\Admin\Auth\Database\Administrator());

        $form->image('qrcode', '二维码');

        return $form;
    }
}
