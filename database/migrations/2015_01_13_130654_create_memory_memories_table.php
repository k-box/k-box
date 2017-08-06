<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Handle associations between navigation memories
 */
class CreateMemoryMemoriesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memory_memories', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('memory_id')->unsigned()->nullable();

            $table->foreign('memory_id')->references('id')->on('navigation_memories');

            /**
             * The navigation memory that contains the memory specified by memory_id
             */
            $table->bigInteger('memory_container_id')->unsigned()->nullable();

            $table->foreign('memory_container_id')->references('id')->on('navigation_memories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('memory_memories');
    }
}
