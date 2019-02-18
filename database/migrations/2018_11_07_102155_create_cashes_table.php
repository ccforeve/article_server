<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index()->comment('提现用户');
            $table->unsignedTinyInteger('type')->comment('提现类型（1：微信提现，2：支付宝提现）');
            $table->decimal('price')->comment('提现金额');
            $table->string('mch_billno', 28)->nullable()->comment('红包订单号');
            $table->unsignedTinyInteger('state')->default(0)->comment('提现状态（1：到账成功，2：到账失败）');
            $table->dateTime('over_at')->nullable()->comment('到账时间');
            $table->string('remark')->nullable()->comment('备注');
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
        Schema::dropIfExists('cashes');
    }
}
