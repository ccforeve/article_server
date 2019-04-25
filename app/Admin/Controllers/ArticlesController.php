<?php

namespace App\Admin\Controllers;

use App\Models\Article;
use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ArticlesController extends Controller
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
            ->header('文章')
            ->description('列表')
            ->body($this->grid());
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
            ->header('文章')
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
            ->header('文章')
            ->description('添加')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Article);
        $grid->model()->latest('id');

        $grid->id('Id');
        $grid->title('标题')->limit(50);
        $grid->url('文章链接')->display(function () {
            return "http://btl.yxcxin.com/article_detail/{$this->id}/public";
        });
        $grid->category()->title('分类');
        $grid->read_count('阅读数');
        $grid->share_count('分享数');
        $grid->show_at('显示时间');
        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');

        $grid->disableExport();

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->equal('category_id', '分类')->select(ArticleCategory::query()->pluck('title', 'id'))->default(1);
            $filter->like('title', '标题');
        });

        $grid->actions(function ($action) {
            $action->disableView();
        });

        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Article);

        $form->text('title', '标题')->rules('required', ['标题不可为空']);
        $form->cropper('cover', '封面')->uniqueName();
        $form->multipleImage('covers', '多封面')->help('可选');
        $form->select('category_id', '类型')->options(ArticleCategory::all()->pluck('title', 'id'));
        $form->number('read_count', '阅读数');
        $form->number('share_count', '分享数');
        $form->number('like_count', '喜欢数');
        $form->switch('cover_state', '是否显示多图封面');
        $form->datetime('show_at', '显示时间');
        $form->textarea('desc', '描述');
        $form->UEditor('detail', '文章详情');

        $form->saving(function (Form $form) {
            $form->detail = str_replace('crossorigin="anonymous"', '', $form->detail);
        });
        return $form;
    }
}
