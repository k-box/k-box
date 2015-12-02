<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('document_groups', function(Blueprint $table)
		{
			$table->bigIncrements('id');


			$table->bigInteger('document_id')->unsigned();

			$table->bigInteger('group_id')->unsigned();

			$table->foreign('document_id')->references('id')->on('document_descriptors');

			$table->foreign('group_id')->references('id')->on('groups');

		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('document_groups');
	}

}
