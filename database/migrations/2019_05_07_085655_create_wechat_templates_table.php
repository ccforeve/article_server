<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('template_id')->comment('模板id');
            $table->string('url')->comment('跳转链接');
            $table->string('first')->comment('头部');
            $table->text('keyword')->comment('模板消息内容');
            $table->string('remark')->comment('底部');
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
        Schema::dropIfExists('wechat_templates');
    }
}
