<?php

namespace App\Admin\Controllers;

use App\Models\Brand;
use App\Models\Poster;
use App\Http\Controllers\Controller;
use App\Models\PosterCategory;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class PosterController extends Controller
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
            ->header('美图列表')
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
            ->header('美图详情')
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
            ->header('编辑美图')
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
            ->header('新增美图')
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
        $grid = new Grid(new Poster);

        $grid->model()->latest('id');

        $grid->id('Id');
        $grid->image_url('美图')->image(100, 100);
        $grid->title('标题')->editable();
        $grid->poster()->name('分类');
        $grid->created_at('新增时间');
        $grid->updated_at('更新时间');

        $grid->disableExport();
        $grid->disableRowSelector();

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
        $show = new Show(Poster::findOrFail($id));

        $show->id('Id');
        $show->image_url('美图')->image();
        $show->title('标题');
        $show->poster()->name('分类');
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
        $form = new Form(new Poster);

        $form->image('image_url', '美图');
        $form->text('title', '标题');
        $form->select('poster_type', '类型')->options(['category' => '美图类型', 'brand' => '品牌'])->load('poster_id', '/admin/poster/type');
        $form->select('poster_id', '类型列表');

        return $form;
    }

    public function getType( Request $request )
    {
        switch ($request->q) {
            case 'category':
                $lists = PosterCategory::all(['id', \DB::raw('name as text')]);
                break;
            case 'brand':
                $lists = Brand::all(['id', \DB::raw('name as text')]);
                break;
        }

        return response()->json($lists);
    }
}
