<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_articles', function (Blueprint $table) {
            $table->increments('id')->comment('用户文章表');
            $table->unsignedInteger('user_id')->index()->comment('用户id');
            $table->unsignedInteger('article_id')->index()->comment('文章id');
            $table->unsignedInteger('read_count')->default(0)->comment('被阅读数');
            $table->unsignedInteger('share_count')->default(0)->comment('被分享数');
            $table->unsignedInteger('like_count')->default(0)->comment('喜欢数');
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
        Schema::dropIfExists('user_articles');
    }
}
