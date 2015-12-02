<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeoplegroupToUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('peoplegroup_to_user', function(Blueprint $table)
		{
			$table->bigIncrements('id');

			$table->bigInteger('peoplegroup_id')->unsigned();

			$table->foreign('peoplegroup_id')->references('id')->on('peoplegroup')->onDelete('cascade');

			$table->bigInteger('user_id')->unsigned();

			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$table->dropForeign('peoplegroup_to_user_user_id_foreign');
		$table->dropForeign('peoplegroup_to_user_peoplegroup_id_foreign');
		Schema::drop('peoplegroup_to_user');
	}

}
