<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ArticleExamine;
use App\Models\GoodArticle;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class GoodArticleController extends Controller
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
            ->header('好文章列表')
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
            ->header('好文章详情')
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
        $grid = new Grid(new GoodArticle);

        $grid->model()->latest('id');

        $grid->id('Id');
        $grid->user()->nickname('用户');
        $grid->url('好文章链接')->limit(30);
        $grid->remark('备注');
        $grid->state('状态')->display(function ($value) {
            return $value ? '已审核' : '未审核';
        });
        $grid->examine_url('审核后文章链接')->limit(30);
        $grid->examine_at('审核时间');
        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');

        $grid->disableRowSelector();
        $grid->disableExport();
        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            $actions->disableEdit();
            if($actions->row->state == 0) {
                $actions->append(new ArticleExamine($actions->getKey()));
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
        $show = new Show(GoodArticle::findOrFail($id));

        $show->id('Id');
        $show->user()->nickname('用户');
        $show->url('美图');
        $show->remark('备注');
        $show->state('状态')->as(function ($value) {
            return $value ? '已审核' : '未审核';
        });
        $show->examine_url('审核后文章链接');
        $show->examine_at('审核时间');
        $show->created_at('创建时间');
        $show->updated_at('更新时间');

        return $show;
    }

    public function examine( Request $request, $id )
    {
        GoodArticle::query()->where('id', $id)->update([
            'state' => 1,
            'examine_url' => $request->article_url,
            'examine_at' => now()->toDateString(),
        ]);
    }
}
