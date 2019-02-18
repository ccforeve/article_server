<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFootprintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('footprints', function (Blueprint $table) {
            $table->increments('id')->comment('访问足迹表');
            $table->unsignedInteger('user_id')->index()->comment('文章所属用户id');
            $table->unsignedInteger('user_article_id')->index()->comment('文章id');
            $table->unsignedInteger('see_user_id')->comment('查看用户id');
            $table->unsignedInteger('share_id')->default(0)->comment('上一条足迹id');
            $table->unsignedTinyInteger('type')->comment('类型（1：阅读，2：分享）');
            $table->unsignedInteger('residence_time')->nullable()->comment('停留时间');
            $table->string('from')->nullable()->comment('查看来源');
            $table->unsignedTinyInteger('new')->default(0)->comment('新访客（1：已查看）');
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
        Schema::dropIfExists('footprints');
    }
}
