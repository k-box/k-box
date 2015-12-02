<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSharedTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shared', function(Blueprint $table)
		{
			$table->bigIncrements('id');

			/**
			 * The user that has put a star on the document
			 */
			$table->bigInteger('user_id')->unsigned();

			/**
			 * Shared with (user_id)
			 */
			$table->bigInteger('shared_with')->unsigned();

			$table->string('token', 128)->unique();

			$table->timestamps();

			$table->bigInteger('shareable_id')->unsigned();
			$table->string('shareable_type');

			$table->dateTime('expiration')->nullable();
			
			$table->unique(array('token', 'shared_with', 'shareable_id', 'shareable_type'));


			$table->foreign('user_id')->references('id')->on('users');

			$table->foreign('shared_with')->references('id')->on('users');

			$table->index(array('shareable_id', 'shareable_type'));

			$table->index('user_id');

			$table->index('shared_with');

		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shared');
	}

}
