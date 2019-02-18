<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageFamiliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_families', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('被咨询用户');
            $table->unsignedInteger('submit_user_id')->comment('咨询用户');
            $table->unsignedTinyInteger('type')->comment('类型');
            $table->string('region', 100)->comment('工作地区');
            $table->string('name', 30)->comment('姓名');
            $table->string('phone', 11)->comment('手机号');
            $table->string('family', 100)->comment('家庭结构');
            $table->string('age', 30)->comment('年龄');
            $table->string('income', 30)->comment('年收入');
            $table->unsignedTinyInteger('gender')->comment('性别');
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
        Schema::dropIfExists('message_families');
    }
}
