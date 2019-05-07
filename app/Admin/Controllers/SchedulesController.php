<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\SendMessage;
use App\Models\Article;
use App\Models\Schedule;
use App\Http\Controllers\Controller;
use App\Models\PosterCategory;
use App\Models\User;
use App\Models\WechatTemplate;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class SchedulesController extends Controller
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
            ->header('定时发送消息')
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
            ->header('定时发送消息')
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
            ->header('定时发送消息')
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
            ->header('定时发送消息')
            ->description('新增')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Schedule());

        $grid->model()->latest('id');

        $grid->id('Id');
        $grid->type('发送类型')->using([1 => '客服文字消息', 2 => '客服图文消息', 3 => '模板消息']);
        $grid->template_id('模板列表id')->using(WechatTemplate::all(['id', 'name'])->pluck('name', 'id')->all());
        $grid->content('发送内容')->limit(50);
        $grid->send_at('发送时间');
        $grid->created_at('新增时间');
        $grid->updated_at('更新时间');

        $grid->disableExport();
        $grid->disableRowSelector();

        $grid->actions(function ($action) {
            $action->append(new SendMessage($action->getKey()));
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
        $show = new Show(Schedule::findOrFail($id));

        $show->id('Id');
        $show->type('发送类型')->using([1 => '客服文字消息', 2 => '客服图文消息', 3 => '模板消息']);
        $show->template_id('模板列表id')->using(WechatTemplate::all(['id', 'name'])->pluck('name', 'id')->all());
        $show->content('发送内容')->limit(50);
        $show->send_at('发送时间');
        $show->created_at('创建时间');
        $show->updated_at('更新时间');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Schedule);

        $form->radio('type', '发送类型')->options([1 => '客服文字消息', 2 => '客服图文消息', 3 => '模板消息'])->default(1);
        $form->textarea('content', '发送内容')->placeholder('模板消息可不填，图文消息只需填写文章id');
        $form->select('template_id', '选择模板消息')->options(WechatTemplate::all()->pluck('name', 'id'));
        $form->datetime('send_at', '发送时间');

        return $form;
    }

    /**
     * 发送消息
     * @param Request $request
     * @param Schedule $schedule
     */
    public function sendTest( Request $request, Schedule $schedule )
    {
        switch ($schedule->type){
            case 1:
                message($request->openid, 'text', $schedule->content);
                break;
            case 2:
                $article = Article::query()->where('id', $schedule->content)->first(['id', 'title', 'cover', 'desc', 'product_id']);
                $item = [
                    'title' => $article->title,
                    'description' => $article->desc,
                    'url' => "http://btl.yxcxin.com/article_detail/{$article->id}/public",
                    'image' => $article->cover
                ];
                if($article->product_id) {
                    $item['image'] = "http:" . str_replace('/p/', '/pxs/', $article->cover);
                }
                message($request->openid, 'new_item', $item);
                break;
            case 3:
                $template = WechatTemplate::query()->find($schedule->template_id);
                $message = [
                    "first" => [$template->first['message'], $template->first['color']],
                    "remark" => [$template->remark['message'], $template->remark['color']]
                ];
                foreach ($template->keyword as $key => $item) {
                    if($item['message'] == 'date') {
                        $item['message'] = now()->toDateString();
                    }
                    $keyword = 'keyword' . ($key + 1);
                    $message[$keyword] = $item['message'];
                }
                template_message($request->openid, $message, $template->template_id, $template->url);
                break;
        }
    }
}
