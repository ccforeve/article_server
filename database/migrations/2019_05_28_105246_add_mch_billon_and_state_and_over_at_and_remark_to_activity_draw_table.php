<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMchBillonAndStateAndOverAtAndRemarkToActivityDrawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_draw', function (Blueprint $table) {
            $table->string('mch_billno')->nullable()->comment('红包单号');
            $table->unsignedTinyInteger('state')->default(0)->comment('状态');
            $table->timestamp('over_at')->nullable()->comment('完成时间');
            $table->string('remark')->nullable()->comment('备注');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_draw', function (Blueprint $table) {
            //
        });
    }
}
