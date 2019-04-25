<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ArticleExamine;
use App\Models\ExtensionArticle;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class ExtensionArticleController extends Controller
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
            ->header('好文章')
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
            ->header('好文章')
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
        $grid = new Grid(new ExtensionArticle());

        $grid->model()->latest('id');

        $grid->id('Id');
        $grid->user()->nickname('用户');
        $grid->url('好文章链接');
        $grid->examine('状态')->display(function ($value) {
            return $value ? '已审核' : '未审核';
        });
        $grid->examine_at('审核时间');
        $grid->article_id('审核文章id');
        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');

        $grid->disableRowSelector();
        $grid->disableExport();
        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            $actions->disableEdit();
            if($actions->row->examine == 0) {
                $actions->prepend(new ArticleExamine($actions->getKey()));
            }
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
        $show = new Show(ExtensionArticle::findOrFail($id));

        $show->id('Id');
        $show->user()->nickname('用户');
        $show->url('链接');
        $show->examine('状态')->as(function ($value) {
            return $value ? '已审核' : '未审核';
        });
        $show->created_at('创建时间');
        $show->updated_at('更新时间');

        return $show;
    }

    /**
     * 审核好文章
     * @param Request $request
     * @param ExtensionArticle $article
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function examine( Request $request, ExtensionArticle $article )
    {
        $article->examine = 1;
        $article->examine_at = now()->toDateTimeString();
        $article->article_id = $request->article_id;
        $article->save();

        $url = "http://btl.yxcxin.com/article_detail/{$request->article_id}/public";
        $message = [
            "first" => "您好，你提交的好文章已通过审核",
            "keyword1" => '通过审核',
            "keyword2" => now()->toDateTimeString(),
            "remark" => "您可以点详情查看文章"
        ];
        template_message($article->user->openid, $message, config('wechat.template.examine'), $url);
    }
}
