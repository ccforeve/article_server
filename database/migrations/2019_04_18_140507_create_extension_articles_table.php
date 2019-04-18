<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExtensionArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extension_articles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('推荐用户id');
            $table->string('url')->comment('推荐url');
            $table->unsignedTinyInteger('examine')->default(0)->comment('审核（1：已审核）');
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
        Schema::dropIfExists('extension_articles');
    }
}
