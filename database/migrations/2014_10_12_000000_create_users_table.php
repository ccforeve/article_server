<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('openid', 100)->unique()->comment('微信openid');
            $table->unsignedTinyInteger('subscribe')->default(0)->comment('1：关注');
            $table->string('nickname')->comment('微信昵称');
            $table->string('avatar')->comment('微信头像');
            $table->string('phone')->nullable()->comment('手机号');
            $table->string('wechat')->nullable()->comment('微信号');
            $table->unsignedTinyInteger('sex')->default(0)->comment('性别（1：男，2：女）');
            $table->unsignedInteger('brand_id')->default(0)->comment('所属品牌');
            $table->string('employed_area', 100)->nullable()->comment('从业地区');
            $table->string('profession', 30)->nullable()->comment('职业');
            $table->string('qrcode')->nullable()->comment('微信二维码');
            $table->string('ali_account')->nullable()->comment('支付宝账号');
            $table->string('ali_name')->nullable()->comment('支付宝姓名');
            $table->unsignedTinyInteger('type')->default(0)->comment('用户类型（1：经销商）');
            $table->unsignedInteger('superior')->default(0)->comment('推荐上级');
            $table->dateTime('extension_at')->nullable()->comment('被推广时间');
            $table->unsignedTinyInteger('extension_type')->default(0)->comment('被推广方式');
            $table->unsignedInteger('integral_scale')->default(0)->comment('第一层佣金比例');
            $table->dateTime('member_up_at')->nullable()->comment('开通会员时间');
            $table->dateTime('member_lock_at')->nullable()->comment('会员过期时间');
            $table->dateTime('subscribe_at')->nullable()->comment('关注公众号时间');
            $table->dateTime('perfect_at')->nullable()->comment('完善资料时间');
            $table->unsignedTinyInteger('receive_message')->default(0)->comment('是否接收公众号消息（1：不接收）');
            $table->unsignedTinyInteger('state')->default(0)->comment('账户状态（1：禁用）');
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
        Schema::dropIfExists('users');
    }
}
