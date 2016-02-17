<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMicrositesContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('microsite_contents', function(Blueprint $table)
		{
			$table->bigIncrements('id');
            
            $table->bigInteger('microsite_id')->unsigned();
            
            $table->string('language', 5)->default('en');
            
            $table->string('title', 255)->nullable(); // if it is a page a title could be a good idea
            $table->string('slug', 255)->default('');
            
			$table->longText('content');
            
            $table->integer('type')->default(1);
            
            $table->bigInteger('user_id')->unsigned();
            
			$table->timestamps();
            $table->softDeletes();
            
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->foreign('microsite_id')->references('id')->on('microsites')->onDelete('cascade');
            
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('microsite_contents');
    }
}
