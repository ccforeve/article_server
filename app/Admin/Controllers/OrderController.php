<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Refund;
use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Website;
use Carbon\Carbon;
use EasyWeChat\Payment\Application;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Pay;

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
            ->header('订单管理')
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
            ->header('订单')
            ->description('详情')
            ->body($this->detail($id));
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

        $grid->id('order_Id');
        $grid->user()->id('用户id');
        $grid->user()->nickname('用户');
        $grid->user()->phone('手机');
        $grid->price('价格');
        $grid->month('会员月数');
        $grid->state('状态')->using([0 => '未支付', 1 => '已支付', 2 => '支付失败']);
        $grid->pay_type('支付类型')->using([1 => '微信', 2 => '支付宝']);
        $grid->pay_at('支付时间');
        $grid->superiorUser()->nickname('推荐用户');
        $grid->superior_rate('佣金(元)');
        $grid->refund_state('退款状态')->display(function ($value) {
            if($this->state) {
                return $value ? '已退款' : '未退款';
            }
        });
        $grid->created_at('下单时间');

        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableRowSelector();

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            if($actions->row->state && !$actions->row->refund_state) {
                $actions->append(new Refund($actions->row));
            }
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
            $filter->equal('refund_state', '退款状态')->radio([0 => '未退款', 1 => '已退款']);
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
        $show->superiorUser()->nickname('推荐用户');
        $show->superior_rate('一级佣金')->badge();
        $show->refund_state('退款状态')->as(function ($value) {
            return $value ? '已退款' : '未退款';
        });
        $show->refund_at('退款时间');
        $show->created_at('下单时间');

        return $show;
    }

    /**
     * 退款
     * @param Application $app
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function refund( Application $app, Request $request, Order $order )
    {
        if ($order->pay_type == 1) {
            return $this->wechatRefund($request, $app, $order);
        }
        return $this->aliRefund($request, $order);
    }

    /**
     * 微信退款
     * @param $request
     * @param $app
     * @param $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function wechatRefund( $request, $app, $order )
    {
        $orderNo = $order->order_id;
        $refundNo = date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $orderPrice = $order->price * 100;
        $refundPrice = $request->refund_fee * 100;
        $ret = $app->refund->byOutTradeNumber($orderNo, $refundNo, $orderPrice, $refundPrice);
        if($ret['result_code'] == 'SUCCESS' && $ret['return_code'] == 'SUCCESS') {
            return $this->refundOperation($request, $order);
        } else {
            return response()->json(['code' => $ret['err_code'], 'message' => $ret['err_code_des']]);
        }
    }

    /**
     * 支付宝退款
     * @param $request
     * @param $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function aliRefund( $request, $order )
    {
        $data = [
            'out_trade_no' => $order->order_id,
            'refund_amount' => $request->refund_fee
        ];

        $ret = Pay::alipay()->refund($data);
        if($ret['code'] == 10000 && $ret['msg'] == 'Success' && $ret['fund_change'] === 'Y') {
            return $this->refundOperation($request, $order);
        }

        info('支付宝退款失败：', [$ret]);
        return response()->json(['code' => 417, 'message' => '退款出错']);
    }

    /**
     * 退款操作
     * @param $request
     * @param $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function refundOperation( $request, $order )
    {
        $order->refund_state = 1;
        $order->refund_fee = $request->refund_fee;
        $order->refund_remark = $request->refund_remark;
        $order->refund_at = now();
        $order->save();
        $user = User::query()->find($order->user_id);
        $user->member_lock_at = Carbon::parse($user->member_lock_at)->subMonths($order->month);
        $user->save();

        return response()->json(['code' => Response::HTTP_OK, 'message' => '退款成功']);
    }
}
