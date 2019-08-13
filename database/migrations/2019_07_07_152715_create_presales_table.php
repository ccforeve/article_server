<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePresalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presales', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('admin_id')->index()->comment('所属后台用户id');
            $table->unsignedInteger('user_id')->index()->comment('用户id');
            $table->unsignedInteger('order_id')->nullable()->index()->comment('订单id');
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
        Schema::dropIfExists('presales');
    }
}
