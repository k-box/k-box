<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            
            $table->string('name', 255)->unique();
            $table->text('description')->nullable();
            $table->string('avatar')->nullable(); //file path of an avatar image
            
            
            // the project manager
            $table->bigInteger('user_id')->unsigned();
            
            // the root institutional collection of the project
            $table->bigInteger('collection_id')->unsigned();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->foreign('collection_id')->references('id')->on('groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
