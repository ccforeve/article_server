<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExamineAtAndArticleIdToExtensionArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('extension_articles', function (Blueprint $table) {
            $table->timestamp('examine_at')->nullable()->after('examine')->comment('审核时间');
            $table->tinyInteger('article_id')->nullable()->after('examine')->comment('文章id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('extension_articles', function (Blueprint $table) {
            //
        });
    }
}
