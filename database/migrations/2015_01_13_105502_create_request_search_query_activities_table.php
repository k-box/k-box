<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestSearchQueryActivitiesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_search_query_activities', function (Blueprint $table) {
            $table->bigIncrements('id');

            /**
             * The terms searched
             */
            $table->string('terms');

            /**
             * The search visibility
             */
            $table->string('visibility');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_search_query_activities');
    }
}
