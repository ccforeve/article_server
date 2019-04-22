<?php

namespace App\Admin\Controllers;

use App\Models\Payment;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class PaymentController extends Controller
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
            ->header('价格')
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
            ->header('价格')
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
            ->header('价格')
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
            ->header('价格')
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
        $grid = new Grid(new Payment());

        $grid->id('Id');
        $grid->price('现价')->editable();
        $grid->original_price('市场价')->editable();
        $grid->title('支付标题')->editable();
        $grid->month('月份')->editable();
        $grid->extension('推荐')->editable('select', [0 => '否', 1 => '是']);
        $grid->created_at('新增时间');
        $grid->updated_at('更新时间');

        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableFilter();
        $grid->disablePagination();

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
        $show = new Show(Payment::findOrFail($id));

        $show->id('Id');
        $show->price('现价');
        $show->original_price('市场价');
        $show->title('支付标题');
        $show->month('月份');
        $show->extension('推荐')->using([0 => '否', 1 => '是']);
        $show->created_at('新增时间');
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
        $form = new Form(new Payment);

        $form->number('price', '现价');
        $form->number('original_price', '市场价');
        $form->text('title', '支付标题');
        $form->number('month', '月份');
        $form->radio('extension', '推荐')->options([0 => '否', 1 => '是']);

        return $form;
    }
}
