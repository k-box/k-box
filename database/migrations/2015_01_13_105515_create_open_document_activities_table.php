<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOpenDocumentActivitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('open_document_activities', function(Blueprint $table)
		{
			$table->bigIncrements('id');

			$table->bigInteger('document_id')->unsigned();

			/**
			 * The activity that is the hierarchical parent of this activity.
			 *
			 * E.g., a document open might be executed after a search result activity, so here is the ID of
			 * the search result activity
			 */
			$table->bigInteger('originating_activity')->unsigned();
			
			$table->foreign('document_id')->references('id')->on('document_descriptors');

			$table->foreign('originating_activity')->references('id')->on('activities');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('open_document_activities');
	}

}
