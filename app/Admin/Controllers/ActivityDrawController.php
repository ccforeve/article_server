<?php

namespace App\Admin\Controllers;

use App\Models\ActivityDraw;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ActivityDrawController extends Controller
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
            ->header('抽奖')
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
            ->header('抽奖')
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
            ->header('抽奖')
            ->description('修改')
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
            ->header('抽奖')
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
        $grid = new Grid(new ActivityDraw);
        $grid->model()->latest('id');

        $grid->id('Id');
        $grid->activity()->name('所属活动');
        $grid->name('页面展示昵称');
        $grid->phone('页面展示手机号');
        $grid->user()->wechat('微信号');
        $grid->prize('奖品')->display(function ($prize) {
            return ActivityDraw::$type[$prize];
        });
//
        $grid->disableExport();
        $grid->disableRowSelector();

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
        });

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();

            $filter->where(function ($filter) {
                $filter->whereHas('user', function ($query) {
                    $query->where('nickname', 'like', "%{$this->input}%")
                    ->orWhere('phone', 'like', "%{$this->input}%")
                    ->orWhere('wechat', 'like', "%{$this->input}%");
                });
            }, '昵称或手机号或微信');
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
        $form = new Form(new ActivityDraw());

        $form->text('name', '姓名')->rules('required', ['姓名不可为空']);


        return $form;
    }
}
