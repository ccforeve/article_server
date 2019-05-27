<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\CashOperation;
use App\Models\Cash;
use App\Http\Controllers\Controller;
use App\Models\Website;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CashesController extends Controller
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
            ->header('提现列表')
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
            ->header('Detail')
            ->description('description')
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
            ->header('Edit')
            ->description('description')
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
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Cash);
        $grid->model()->latest('id');

        $grid->id('Id');
        $grid->user()->nickname('用户昵称')->limit(10);
        $grid->user()->phone('手机号');
        $grid->user()->wechat('微信号');
//        $grid->user()->ali_account('支付宝账号');
//        $grid->user()->ali_name('支付宝姓名');
        $grid->price('金额');
        $grid->state('提现状态')->display(function ($value) {
            switch ($value) {
                case 0:
                    $state = "<color style='color: red'>未提现</color>";break;
                case 1:
                    $state = "<color style='color: green'>提现成功</color>";break;
                case 2:
                    $state = '提现是吧';break;
            }
            return $state;
        });
        $grid->over_at('提现完成时间');
        $grid->remark('备注');
        $grid->created_at('申请时间');

        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableRowSelector();

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
            if($actions->row->state == 0) {
                $actions->append(new CashOperation($actions->getKey()));
            }
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
            $filter->where(function ($filter) {
                $filter->whereHas('user', function ($query) {
                    $query->where('ali_account', 'like', "%{$this->input}%")
                        ->orWhere('ali_name', 'like', "%{$this->input}%");
                });
            }, '支付宝账号或支付宝姓名');
            $filter->equal('type', '提现类型')->radio([1 => '微信', 2 => '支付宝']);
            $filter->equal('state', '提现状态')->radio([0 => '未提现', 1 => '提现成功', 2 => '提现失败']);
            $filter->equal('created_at', '申请时间')->date();
        });

        $grid->perPages([15, 20]);

        return $grid;
    }
}
