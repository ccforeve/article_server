<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Models\PhotoType;
use App\Http\Controllers\Controller;
use App\Models\Users;
use Carbon\Carbon;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ReportController extends Controller
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
        $users = Users::all();
        $user['today'] = 0;
        $user['yesterday'] = 0;
        $user['before_yesterday'] = 0;
        $user['this_month'] = 0;
        $user['last_month_day'] = 0;
        $user['last_month'] = 0;
        $user['before_last_month'] = 0;
        $user['all'] = $users->count();
        $user = $this->count($users, $user);

        $perfect_users = Users::query()->where('phone', '<>', '')->get();
        $perfect_user['today'] = 0;
        $perfect_user['yesterday'] = 0;
        $perfect_user['before_yesterday'] = 0;
        $perfect_user['this_month'] = 0;
        $perfect_user['last_month_day'] = 0;
        $perfect_user['last_month'] = 0;
        $perfect_user['before_last_month'] = 0;
        $perfect_user['all'] = $perfect_users->count();
        $perfect_user = $this->count($perfect_users, $perfect_user);

        $orders = Order::all();
        $order['today'] = 0;
        $order['yesterday'] = 0;
        $order['before_yesterday'] = 0;
        $order['this_month'] = 0;
        $order['last_month_day'] = 0;
        $order['last_month'] = 0;
        $order['before_last_month'] = 0;
        $order['all'] = $orders->count();
        $order = $this->count($orders, $order);

        $open_up_orders = Order::query()->where(['state' => 1, 'refund_state' => 0])->get();
        $open_up_order['today'] = 0;
        $open_up_order['yesterday'] = 0;
        $open_up_order['before_yesterday'] = 0;
        $open_up_order['this_month'] = 0;
        $open_up_order['last_month_day'] = 0;
        $open_up_order['last_month'] = 0;
        $open_up_order['before_last_month'] = 0;
        $open_up_order['all'] = $open_up_orders->count();
        $open_up_order = $this->count($open_up_orders, $open_up_order);

        $user_membership_rate['today'] = $open_up_order['today'] != 0 && $perfect_user['today'] != 0 ? number_format($open_up_order['today'] / $perfect_user['today'], 2) : 0;
        $user_membership_rate['yesterday'] = $open_up_order['yesterday'] != 0 && $perfect_user['yesterday'] != 0 ? number_format($open_up_order['yesterday'] / $perfect_user['yesterday'], 2) : 0;
        $user_membership_rate['before_yesterday'] = $open_up_order['before_yesterday'] != 0 && $perfect_user['before_yesterday'] != 0 ? number_format($open_up_order['before_yesterday'] / $perfect_user['before_yesterday'], 2) : 0;
        $user_membership_rate['this_month'] = $open_up_order['this_month'] != 0 && $perfect_user['this_month'] != 0 ? number_format($open_up_order['this_month'] / $perfect_user['this_month'], 2) : 0;
        $user_membership_rate['last_month_day'] = $open_up_order['last_month_day'] != 0 && $perfect_user['last_month_day'] != 0 ? number_format($open_up_order['last_month_day'] / $perfect_user['last_month_day'], 2) : 0;
        $user_membership_rate['last_month'] = $open_up_order['last_month'] != 0 && $perfect_user['last_month'] != 0 ? number_format($open_up_order['last_month'] / $perfect_user['last_month'], 2) : 0;
        $user_membership_rate['before_last_month'] = $open_up_order['before_last_month'] != 0 && $perfect_user['before_last_month'] != 0 ? number_format($open_up_order['before_last_month'] / $perfect_user['before_last_month'], 2) : 0;
        $user_membership_rate['all'] = $open_up_order['all'] != 0 && $perfect_user['all'] != 0 ? number_format($open_up_order['all'] / $perfect_user['all'], 2) : 0;

        $membership_rate['today'] = $open_up_order['today'] != 0 && $order['today'] != 0 ? number_format($open_up_order['today'] / $order['today'], 2) : 0;
        $membership_rate['yesterday'] = $open_up_order['yesterday'] != 0 && $order['yesterday'] != 0 ? number_format($open_up_order['yesterday'] / $order['yesterday'], 2) : 0;
        $membership_rate['before_yesterday'] = $open_up_order['before_yesterday'] != 0 && $order['before_yesterday'] != 0 ? number_format($open_up_order['before_yesterday'] / $order['before_yesterday'], 2) : 0;
        $membership_rate['this_month'] = $open_up_order['this_month'] != 0 && $order['this_month'] != 0 ? number_format($open_up_order['this_month'] / $order['this_month'], 2) : 0;
        $membership_rate['last_month_day'] = $open_up_order['last_month_day'] != 0 && $order['last_month_day'] != 0 ? number_format($open_up_order['last_month_day'] / $order['last_month_day'], 2) : 0;
        $membership_rate['last_month'] = $open_up_order['last_month'] != 0 && $order['last_month'] != 0 ? number_format($open_up_order['last_month'] / $order['last_month'], 2) : 0;
        $membership_rate['before_last_month'] = $open_up_order['before_last_month'] != 0 && $order['before_last_month'] != 0 ? number_format($open_up_order['before_last_month'] / $order['before_last_month'], 2) : 0;
        $membership_rate['all'] = $open_up_order['all'] != 0 && $order['all'] != 0 ? number_format($open_up_order['all'] / $order['all'], 2) : 0;

        $order_money['today'] = 0;
        $order_money['yesterday'] = 0;
        $order_money['before_yesterday'] = 0;
        $order_money['this_month'] = 0;
        $order_money['last_month_day'] = 0;
        $order_money['last_month'] = 0;
        $order_money['before_last_month'] = 0;
        $order_money['all'] = $open_up_orders->sum('price');
        $order_money = $this->count($open_up_orders, $order_money, 1);

        return $content
            ->header('数据报表')
            ->description('报表')
            ->body(view('admin.report_index', compact('user', 'perfect_user', 'order', 'open_up_order', 'user_membership_rate', 'membership_rate', 'order_money')));
    }

    public function count( $datas, $res, $type = 0 )
    {
        //前天凌晨时间
        $before_yesterday_time = Carbon::yesterday()->subDay();
        //昨日凌晨时间
        $yesterday_time = Carbon::yesterday();
        //今天凌晨时间
        $today_time = Carbon::today();
        //明天凌晨时间
        $tomorrow_time = Carbon::tomorrow();
        //本月初时间
        $this_month_time = Carbon::now()->startOfMonth();
        //下月初时间
        $end_month_time = Carbon::now()->addMonth(1)->startOfMonth();
        //上月初时间
        $last_month_time = Carbon::now()->addMonth(-1)->startOfMonth();
        //上月今天的时间
        $last_month_day_time = Carbon::today()->subMonth()->addDay();
        //前月初时间
        $before_last_month_time = Carbon::now()->addMonth(-2)->startOfMonth();
        foreach ($datas as $data) {
            $created_at = Carbon::parse($data->created_at);
            if($created_at->gt($today_time) && $created_at->lt($tomorrow_time)) {
                if($type) {
                    $res[ 'today' ] += $data->price;
                } else {
                    $res[ 'today' ] += 1;
                }
            }
            if($created_at->gt($yesterday_time) && $created_at->lt($today_time)) {
                if($type) {
                    $res[ 'yesterday' ] += $data->price;
                } else {
                    $res[ 'yesterday' ] += 1;
                }
            }
            if($created_at->gt($before_yesterday_time) && $created_at->lt($yesterday_time)) {
                if($type) {
                    $res[ 'before_yesterday' ] += $data->price;
                } else {
                    $res[ 'before_yesterday' ] += 1;
                }
            }
            if($created_at->gt($this_month_time) && $created_at->lt($end_month_time)) {
                if($type) {
                    $res[ 'this_month' ] += $data->price;
                } else {
                    $res[ 'this_month' ] += 1;
                }
            }
            if($created_at->gt($last_month_time) && $created_at->lt($last_month_day_time)) {
                if($type) {
                    $res[ 'last_month_day' ] += $data->price;
                } else {
                    $res[ 'last_month_day' ] += 1;
                }
            }
            if($created_at->gt($last_month_time) && $created_at->lt($this_month_time)) {
                if($type) {
                    $res[ 'last_month' ] += $data->price;
                } else {
                    $res[ 'last_month' ] += 1;
                }
            }
            if($created_at->gt($before_last_month_time) && $created_at->lt($last_month_time)) {
                if($type) {
                    $res[ 'before_last_month' ] += $data->price;
                } else {
                    $res[ 'before_last_month' ] += 1;
                }
            }
        }

        return $res;
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

}
