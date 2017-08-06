<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemoryActivitiesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memory_activities', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('memory_id')->unsigned()->nullable();

            $table->foreign('memory_id')->references('id')->on('navigation_memories');

            $table->bigInteger('activity_id')->unsigned();
            
            $table->foreign('activity_id')->references('id')->on('activities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('memory_activities');
    }
}
