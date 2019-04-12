<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeyworksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('key_words', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('指定的文字');
            $table->string('cmd')->comment('规则名，或自定义返回结果表主键');
            $table->unsignedTinyInteger('type')->comment('处理类型，0=等于，1=前置，2=中间，3=后置，4=正则，5=包含多个');
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
        Schema::dropIfExists('key_words');
    }
}
