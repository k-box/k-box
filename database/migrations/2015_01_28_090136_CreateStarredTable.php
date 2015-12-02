<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStarredTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('starred', function(Blueprint $table)
		{
			$table->bigIncrements('id');

			/**
			 * The user that has put a star on the document
			 */
			$table->bigInteger('user_id')->unsigned();

			/**
			 * The document that has been starred
			 */
			$table->bigInteger('document_id')->unsigned();

			$table->timestamps();

			/**
			 * Uniqueness contraint: the user cannot put more than one star on the same document
			 */
			$table->unique(array('user_id', 'document_id'));


			$table->foreign('user_id')->references('id')->on('users');

			$table->foreign('document_id')->references('id')->on('document_descriptors');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('starred');
	}

}
