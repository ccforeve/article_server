<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->comment('名称');
            $table->string('alias_name', 200)->default(null)->comment('别名');
            $table->unsignedTinyInteger('kind')->default(0)->comment('产品类型 0-商城产品 1-复消产品 2-报单产品 3-预订产品');
            $table->unsignedInteger('category_id')->index()->comment('分类Id');
            $table->unsignedInteger('parent_category_id')->index()->default(0)->comment('父级分类Id');
            $table->decimal('price', 10, 2)->default(0.00)->comment('销售价');
            $table->decimal('money', 10, 2)->default(0.00)->comment('现金');
            $table->decimal('ticket', 10, 2)->default(0.00)->comment('购物券');
            $table->string('sn', 20)->comment('商品编号');
            $table->string('min_unit', 10)->comment('最小单位');
            $table->string('unit', 10)->comment('进货单位');
            $table->string('sale_unit', 20)->default(null)->comment('销售单位');
            $table->unsignedInteger('multiple')->default(1)->comment('倍率');
            $table->unsignedTinyInteger('state')->default(1)->comment('状态，0-下架，1-在售，9-暂时隐藏（图片未下载）');
            $table->unsignedInteger('local_id')->default(null)->comment('品牌线上产品Id');
            $table->string('cover', 100)->default(null)->comment('图片路径');
            $table->timestamp('listed_at')->nullable()->comment('上市时间');
            $table->unsignedInteger('view_count')->default(0)->comment('查看次数');
            $table->string('friendly_name', 100)->default(null)->comment('友好名称');
            $table->string('spec', 50)->default(null)->comment('规格');
            $table->unsignedTinyInteger('is_convert_price')->default(1)->comment('是否换算价格');
            $table->unsignedInteger('brand_id')->default(1)->comment('大品牌，如绿叶、三生、国珍');
            $table->string('brand', 30)->default(null)->comment('大品牌下的小品牌');
            $table->string('keywords', 500)->default(null)->comment('关键词');
            $table->unsignedTinyInteger('is_show_price')->default(1)->comment('是否显示在价格表中');
            $table->text('desc')->comment('商品描述');
            $table->text('content')->comment('商品详情');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product');
    }
}
