<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100)->comment('标题');
            $table->string('cover')->comment('单封面图');
            $table->string('covers')->comment('多封面图');
            $table->unsignedTinyInteger('category_id')->comment('所属分类');
            $table->unsignedInteger('brand_id')->default(0)->comment('所属品牌');
            $table->text('detail')->nullable()->comment('文章详情');
            $table->unsignedInteger('read_count')->default(0)->comment('阅读数');
            $table->unsignedInteger('share_count')->default(0)->comment('分享数');
            $table->unsignedInteger('like_count')->default(0)->comment('喜欢数');
            $table->unsignedTinyInteger('cover_state')->default(0)->comment('封面类型（1：显示多图）');
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
        Schema::dropIfExists('articles');
    }
}
