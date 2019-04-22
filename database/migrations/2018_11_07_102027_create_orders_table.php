<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id')->comment('订单表');
            $table->unsignedTinyInteger('public_id')->index()->comment('所属公众号订单');
            $table->unsignedInteger('user_id')->index()->comment('下单用户');
            $table->string('order_id', 32)->comment('订单号');
            $table->decimal('price')->comment('金额');
            $table->string('title', 100)->comment('支付标题');
            $table->unsignedTinyInteger('month')->comment('会员月数');
            $table->unsignedTinyInteger('state')->default(0)->comment('会员类型（0：未支付，1：已支付，2：支付失败）');
            $table->unsignedTinyInteger('pay_type')->comment('支付类型（1：微信，2：支付宝）');
            $table->dateTime('pay_at')->nullable()->comment('支付时间');
            $table->unsignedInteger('superior')->default(0)->comment('推广用户');
            $table->unsignedTinyInteger('superior_rate')->default(0)->comment('推广佣金');
            $table->unsignedTinyInteger('refund_state')->default(0)->comment('退款状态（1：已退款，2：退款失败）');
            $table->dateTime('refund_at')->nullable()->comment('退款到账时间');
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
        Schema::dropIfExists('orders');
    }
}
