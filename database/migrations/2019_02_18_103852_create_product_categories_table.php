<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 45)->comment('名称');
            $table->unsignedInteger('parent_id')->comment('父类别Id');
            $table->unsignedTinyInteger('enabled')->default(1)->comment('是否启用');
            $table->unsignedInteger('level')->default(1)->comment('级别');
            $table->unsignedInteger('local_id')->default(null)->comment('品牌线上产品类别Id');
            $table->string('cover', 100)->default(null)->comment('图片');
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
        Schema::dropIfExists('product_categories');
    }
}
