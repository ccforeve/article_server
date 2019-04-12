<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeyworkCustomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('key_word_customs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('自定义返回的名称');
            $table->text('response_content')->comment('自定义返回的内容，字段是一个复合字段，多项包含在一个字段中，中间用|分隔');
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
        Schema::dropIfExists('key_word_customs');
    }
}
