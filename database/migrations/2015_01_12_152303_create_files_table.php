<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('files', function(Blueprint $table)
		{
			$table->bigIncrements('id');

			/**
			 * The user that has uploaded the file
			 */
			$table->bigInteger('user_id')->unsigned();

			$table->string('name');

			$table->mediumText('hash');

			$table->bigInteger('size')->unsigned();

			/**
			 * updated_at created_at
			 */
			$table->timestamps();

			/**
			 * Undo your delete.
			 *
			 * use Illuminate\Database\Eloquent\SoftDeletes;
			 *
			 * class User extends Eloquent {
			 *
    		 *    use SoftDeletes;
    		 *
    		 * 	  protected $dates = ['deleted_at'];
    		 *
    		 * 	//...
    		 * 	}
			 * 
			 */
			$table->softDeletes();


			$table->string('thumbnail_path')->nullable();

			/**
			 * The current path where the file is saved
			 */
			$table->string('path');

			/**
			 * The original source of the file (path, url,...)
			 */
			$table->string('original_uri')->default('');

			/**
			 * Save the id of the file that represents the old version
			 */
			$table->bigInteger('revision_of')->nullable()->unsigned();


			$table->unique(array('user_id', 'name', 'path'));


			$table->foreign('user_id')->references('id')->on('users');

			

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('files');
	}

}
