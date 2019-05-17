<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShowToMessageFamiliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('message_families', function (Blueprint $table) {
            $table->unsignedTinyInteger('show')->nullable()->comment('标识是否要删除留言');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('message_families', function (Blueprint $table) {
            //
        });
    }
}
