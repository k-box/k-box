<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMicrositesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('microsites', function(Blueprint $table)
		{
			$table->bigIncrements('id');
            
            $table->bigInteger('project_id')->unsigned();
            
			$table->string('title', 255);
			$table->string('slug', 255)->unique();
            
			$table->text('description')->nullable();
			
            $table->string('logo', 255)->nullable(); // url or path to the file
            $table->string('hero_image', 255)->nullable(); // url or path to the file
            
            $table->string('default_language', 5)->default('en');
            
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('institution_id')->unsigned();
            
			$table->timestamps();
            $table->softDeletes();
            
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            $table->foreign('institution_id')->references('id')->on('institutions');
            
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('microsites');
    }
}
