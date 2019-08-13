<?php

namespace App\Admin\Controllers;

use App\Models\Activity;
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
        $grid->user()->nickname('原用户');
        $grid->name('页面展示昵称');
        $grid->phone('页面展示手机号');
        $grid->user()->wechat('微信号');
        $grid->prize('奖品')->display(function ($prize) {
            return ActivityDraw::$type[$prize];
        });
        $grid->created_at('抽奖时间');
        $grid->remark('备注')->limit(50);

        $grid->disableExport();
        $grid->disableRowSelector();

        $grid->actions(function ($actions) {
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

        $form->select('activity_id', '所属活动')->options(Activity::query()->pluck('name', 'id'))->default(1)->help('默认');
        $form->select('user_id', '所属用户')->options([17 => '老刘', 20 => '李源源', 38 => '万玉亮', 42 => '吴涛', 50 => '钟金春'])->default(17);
        $form->text('name', '页面展示姓名')->rules('required', ['姓名不可为空']);
        $form->text('phone', '页面展示手机号')->rules('required|max:11', ['手机号不能为空', '不能超过11位']);
        $form->select('prize', '抽中奖品')->options([
            1 => '5元现金红包',
            2 => '10元现金红包',
            3 => '20元现金红包',
            4 => '999元现金红包',
            5 => '50元现金红包',
            6 => '华为M5平板电脑',
            7 => '5元现金红包',
            8 => '10元现金红包',
            9 => '华为P30 Pro'
        ])->default(5);

        return $form;
    }
}
