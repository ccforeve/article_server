<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\PresaleContact;
use App\Admin\Extensions\Tools\Distribution;
use App\Http\Controllers\Controller;
use App\Http\Requests\DistributionRequest;
use App\Models\Presale;
use App\Models\User;
use Carbon\Carbon;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class PresalesController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function presale(Content $content)
    {
        return $content
            ->header('售前总列表')
            ->description('列表')
            ->body($this->grid());
    }

    protected function grid()
    {
        $grid = new Grid(new User);
        $grid->model()->where('phone', '<>', '')->latest('id');

        $grid->id('用户Id');
        $grid->nickname('用户名');
        $grid->phone('手机号');
        $grid->wechat('微信号');
        $grid->created_at('注册时间');
        $grid->member_up_at('开通时间');
        $grid->presale()->created_at('分配时间');
        $grid->presale()->admin_id('所属员工')->using(\DB::table('admin_users')->pluck('name', 'id')->all());

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->equal('id', '用户id');
            $filter->where(function ($filter) {
                $filter->where('nickname', 'like', "%$this->input%")
                    ->orWhere('phone', 'like', "%$this->input%")
                    ->orWhere('wechat', 'like', "%$this->input%");
            }, '用户名或手机或微信');
            $filter->where(function ($filter) {
                $filter->whereHas('presale', function ($query) {
                    $query->where('admin_id', $this->input);
                });
            }, '所属员工')->placeholder('万玉亮：2、李源源：5');
        });
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
                $batch->add('李源源', new Distribution(5));
                $batch->add('万玉亮', new Distribution(2));
            });
        });

        $grid->disableCreateButton();
        $grid->disableActions();
        $grid->disableExport();
        $grid->perPages([10, 20]);

        return $grid;
    }

    public function presale_admin(Content $content)
    {
        return $content
            ->header('售前列表')
            ->description('列表')
            ->body($this->grid_admin());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid_admin()
    {
        $grid = new Grid(new Presale);
        $grid->model()->where('admin_id', \Auth::guard('admin')->user()->id)->latest('id');

        $grid->id('Id');
        $grid->user()->id('用户id');
        $grid->user()->nickname('用户名');
        $grid->user()->phone('手机号')->display(function ($phone) {
            if($this->is_contact) {
                return "{$phone}" . "<font style='color: red;font-weight: bolder'>√</font>";
            }
            return "{$phone}";
        });
        $grid->user()->wechat('微信号');
        $grid->user()->created_at('注册时间');
        $grid->user()->member_up_at('开通时间');
        $grid->created_at('分配时间');

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
            if(!$actions->row->is_call) {
                $actions->append(new PresaleContact($actions->row->getKey()));
            }
        });

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->equal('user_id', '用户id');
            $filter->where(function ($filter) {
                $filter->whereHas('user', function ($query) {
                    $query->where('nickname', 'like', "%$this->input%")
                        ->orWhere('phone', 'like', "%$this->input%")
                        ->orWhere('wechat', 'like', "%$this->input%");
                });
            }, '用户名或手机或微信');
        });

        $grid->disableRowSelector();
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->perPages([10, 20]);

        return $grid;
    }

    /**
     * 售前分配
     * @param DistributionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function distribution(DistributionRequest $request)
    {
        if($request->has('ids')) {
            $admin_id = $request->admin_id;
            foreach ($request->ids as $item) {
                $presale = Presale::query()->where('user_id', $item)->first(['id']);
                if($presale->id) {
                    $presale->admin_id = $admin_id;
                    $presale->save();
                } else {
                    Presale::query()->create([
                        'admin_id' => $admin_id,
                        'user_id' => $item
                    ]);
                }
            }

            return response()->json(['code' => 0, 'message' => '分配完成']);
        }

        return response()->json(['code' => 500, 'message' => '分配失败']);
    }

    /**
     * 电联
     * @param Presale $presale
     */
    public function contact(Presale $presale)
    {
        $presale->is_contact = 1;
        $presale->save();
    }

    public function export(Content $content)
    {
        $begin = now()->subDays(29)->startOfDay();
        $end = now()->addDay()->startOfDay();
        $begin_next = now()->subDays(28)->startOfDay();
        $role_users = \DB::table('admin_role_users')->from('admin_role_users as aru')
            ->leftJoin('admin_users as au', 'au.id', '=', 'aru.user_id')
            ->where('role_id', 3)
            ->get();
        $pay_orders = Presale::query()
            ->where('order_id', '<>', '')
            ->whereBetween('updated_at', [$begin, $end])
            ->get();
        $arr = [];
        $arr[0][] = '';
        $days = $end->diffInDays($begin);
        for ($i = 0; $i < $days; $i++) {
            $month = $begin->month;
            $day = $begin->day;
            $arr[0]["{$month}-{$day}"] = "{$month}-{$day}";
            $begin->addDay();
        }
        $arr[0]["总数"] = '总数';
        $begin->subDays(30);
        foreach ($role_users as $key => $role_user) {
            $key = $key + 1;
            $total = 0;
            $arr[$key]['name'] = $role_user->name;
            while ($begin->lt($end)) {
                $arr[$key][$begin->month . '-' . $begin->day] = 0;
                foreach ($pay_orders as $pay_order) {
                    $pay_time = Carbon::parse($pay_order->updated_at);
                    if ($pay_time->gt($begin) && $pay_time->lt($begin_next) && $pay_order->admin_id === $role_user->id) {
                        $arr[$key][$begin->month . '-' . $begin->day]++;
                        $total++;
                    }
                }
                $begin->addDay();
                $begin_next->addDay();
            }
            $arr[$key]['总数'] = $total;
            $begin->subDays(30);
            $begin_next->subDays(30);
        }

        $content->header('报表');
        $content->breadcrumb(
            ['text' => '首页', 'url' => '/admin'],
            ['text' => '报表', 'url' => '']
        );

        $first = '<thead><tr>';
        $other = '<tbody>';
        foreach($arr as $key => $items) {
            $other .= "<tr>";
            foreach ($items as $item) {
                if ($key == 0) {
                    $first .= "<th>{$item}</th>";
                } else {
                    $other .= "<th>{$item}</th>";
                }
            }
            $other .= "</tr>";
        }
        $first .= "</tr></thead>";
        $other .= "</tbody>";
        $contents =  <<<HTML
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-body table-responsive no-padding">
                          <table class="table table-hover">
                            $first
                            
                            $other
                          </table>
                        </div>
                      </div>
                  </div>
              </div>
          </section>
HTML;
        return $content->body($contents);
    }
}
