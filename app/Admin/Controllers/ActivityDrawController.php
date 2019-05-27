<?php

namespace App\Admin\Controllers;

use App\Models\ActivityDraw;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
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
            ->header('活动抽奖')
            ->description('列表')
            ->body($this->grid());
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
        $grid->nickname('页面展示昵称');
        $grid->phone('页面展示手机号');
        $grid->user()->wechat('微信号');
        $grid->type('奖品')->display(function ($type) {
            return ActivityDraw::$type[$type];
        });

        $grid->disableCreateButton();
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
}
