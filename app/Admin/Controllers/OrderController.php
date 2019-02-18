<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Models\Website;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class OrderController extends Controller
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
            ->header('订单列表')
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
            ->header('订单详情')
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
        $grid = new Grid(new Order);
        $grid->model()->latest('id');

        $grid->id('Id');
        $grid->user()->nickname('用户');
        $grid->order_id('订单号');
        $grid->price('价格');
        $grid->month('会员月数');
        $grid->state('状态')->editable('select', [0 => '未支付', 1 => '已支付', 2 => '支付失败']);
        $grid->pay_type('支付类型')->editable('select', [1 => '微信', 2 => '支付宝']);
        $grid->pay_at('支付时间');
        $grid->s_user()->nickname('推荐用户');
        $grid->superior_rate('一级佣金(元)');
        $grid->sup_user()->nickname('推荐用户上级');
        $grid->superior_up_rate('二级佣金(元)');
        $grid->created_at('下单时间');

        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableRowSelector();

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
        });

        $grid->filter(function ($filter) {
            $filter->where(function ($filter) {
                $filter->whereHas('user', function ($query) {
                    $query->where('nickname', 'like', "%{$this->input}%")
                        ->orWhere('phone', 'like', "%{$this->input}%")
                        ->orWhere('wechat', 'like', "%{$this->input}%");
                });
            }, '昵称或手机号或微信');
            $filter->equal('state', '支付状态')->radio([0 => '未支付', 1 => '已支付']);
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
        $show = new Show(Order::findOrFail($id));

        $show->id('Id');
        $show->public()->website_name('站点');
        $show->user()->nickname('用户');
        $show->order_id('订单号');
        $show->price('价格');
        $show->title('支付标题');
        $show->month('会员月数');
        $show->state('支付状态')->as(function ($value) {
            return $value ? '已支付' : '未支付';
        });
        $show->pay_type('支付类型')->as(function ($value) {
            return $value == 1 ? '微信' : '支付宝';
        });
        $show->pay_at('支付时间');
        $show->s_user()->nickname('推荐用户');
        $show->superior_rate('一级佣金')->badge();
        $show->sup_user()->nickname('推荐用户的上级');
        $show->superior_up_rate('二级佣金')->badge();
        $show->refund_state('退款状态')->as(function ($value) {
            return $value ? '已退款' : '未退款';
        });
        $show->refund_at('退款时间');
        $show->created_at('下单时间');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order);

        $form->switch('public_id', 'Public id');
        $form->number('user_id', 'User id');
        $form->text('order_id', 'Order id');
        $form->decimal('price', 'Price');
        $form->text('title', 'Title');
        $form->switch('month', 'Month');
        $form->switch('state', 'State');
        $form->switch('pay_type', 'Pay type');
        $form->datetime('pay_at', 'Pay at')->default(date('Y-m-d H:i:s'));
        $form->number('superior', 'Superior');
        $form->switch('superior_rate', 'Superior rate');
        $form->number('superior_up', 'Superior up');
        $form->switch('superior_up_rate', 'Superior up rate');
        $form->switch('refund_state', 'Refund state');
        $form->datetime('refund_at', 'Refund at')->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
