<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\UserMessage;
use App\Admin\Extensions\UserSendMessage;
use App\Models\Footprint;
use App\Models\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UsersController extends Controller
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
            ->header('用户列表')
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
            ->header('用户详情')
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
            ->header('用户编辑')
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
        $grid = new Grid(new User);
        $grid->model()->when(\Request::get('message') == 1, function ($query) {
            $query->has('footprints');
        })->latest('id');

        $grid->id('Id');
        $grid->subscribe('关注')->editable('select', [0 => '未关注', 1 => '关注']);
        $grid->nickname('昵称');
        $grid->sex('性别')->editable('select', [1 => '男', 2 => '女']);
        $grid->avatar('头像')->image(100, 100);
        $grid->phone('电话');
        $grid->wechat('微信');
        $grid->type('类型')->editable('select', [0 => '普通', 1 => '经销商']);
        $grid->state('状态')->switch(['on' => ['value' => 0, 'text' => '正常'], 'off' => ['value' => 1, 'text' => '禁用']]);
        $grid->member_lock_at('到期时间')->editable('date');
        $grid->superiorUser()->nickname('推荐人');
        $grid->integral_scale('一级佣金比')->editable()->label('default');
        $grid->created_at('注册时间');
        $grid->message('发送留言次数')->label('danger');

        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableRowSelector();

        $grid->tools(function ($tools) {
            $tools->append(new UserMessage());
        });

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            if(count($actions->row->footprints) > 0) {
                $actions->append(new UserSendMessage($actions->row));
            }
        });

        $grid->filter(function($filter) {
            $filter->like('nickname', '昵称');
            $filter->equal('phone', '手机号');
            $filter->equal('wechat', '微信号');
            $filter->equal('subscribe', '关注状态')->radio(['' => '全部', 1 => '关注', 0 => '未关注']);
            $filter->equal('type', '用户类型')->radio(['' => '全部', 0 => '普通用户', 1 => '经销商']);
        });

        $grid->paginate(10);
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
        $show = new Show(User::findOrFail($id));
        $show->id('Id');
        $show->openid('微信Openid');
        $show->subscribe('关注')->using(['0' => '未关注', '1' => '关注']);
        $show->nickname('昵称');
        $show->sex('性别')->using(['2' => '女', '1' => '男', '0' => '无性别']);
        $show->avatar('头像')->image(100, 100);
        $show->phone('手机号');
        $show->wechat('微信号');
        $show->qrcode('微信二维码')->image(100, 100);
        $show->employed_area('从业地区');
        $show->profession('职业');
        $show->type('用户类型')->using(['0' => '普通', '1' => '经销商']);
        $show->state('账号状态')->using(['0' => '正常', '1' => '禁用']);
        $show->superiorUser()->nickname('推荐用户');
        $show->extension_at('被推荐时间');
        $show->extension_type('推荐类型')->using([0 => '无', 1 => '文章', 2 => '邀请卡', 3 => '海报']);
        $show->member_up_at('开通会员时间');
        $show->member_lock_at('会员到期时间');
        $show->integral_scale('一级佣金比例')->badge();
        $show->subscribe_at('关注公众号时间');
//        $show->perfect_at('完善资料时间');
        $show->message_send('是否接收公众号消息')->using(['0' => '接收', '1' => '不接收']);
        $show->ali_account('支付宝账号');
        $show->ali_name('支付宝认证姓名');
        $show->created_at('注册时间');
        $show->updated_at('编辑资料时间');

        $show->panel()->tools(function ($tool) {
            $tool->disableDelete();
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);
        $form->tools(function ($tool) {
            $tool->disableDelete();
        });
        $form->switch('subscribe', '是否关注公众号')->states(['on' => ['value' => 1, 'text' => '关注'], 'off' => ['value' => 0, 'text' => '未关注']]);
        $form->text('nickname', '昵称');
        $form->radio('sex', '性别')->options([0 => '无性别', 1 => '男', 2 => '女']);
        $form->image('avatar', '头像');
        $form->mobile('phone', '手机号');
        $form->text('wechat', '微信号');
        $form->image('qrcode', '微信二维码');
        $form->text('employed_area', '从业地区');
        $form->text('profession', '职业');
        $form->radio('type', '用户类型')->options([0 => '普通用户', 1 => '经销商']);
        $form->switch('state', '账号状态')->states(['on' => ['value' => 0, 'text' => '正常'], 'off' => ['value' => 1, 'text' => '禁用']]);
        $form->datetime('extension_at', '被推荐时间');
        $form->radio('extension_type', '被推荐方式')->options([0 => '无', 1 => '文章', 2 => '邀请卡', 3 => '海报']);
        $form->datetime('member_up_at', '会员开通时间');
        $form->datetime('member_lock_at', '会员到期时间');
        $form->number('integral_scale', '一级佣金比例');
        $form->datetime('subscribe_at', '关注时间');
        $form->datetime('perfect_at', '完善资料时间');
        $form->switch('receive_message', '是否接收公众号消息')->states(['on' => ['value' => 0, 'text' => '接收'], 'off' => ['value' => 1, 'text' => '不接收']]);
        $form->text('ali_account', '支付宝账号');
        $form->text('ali_name', '支付宝认证姓名');

        $form->saving(function (Form $form) {
            if($form->type == 1) {
                $form->member_lock_at = now()->addYears(100);
            }
        });

        return $form;
    }
}
