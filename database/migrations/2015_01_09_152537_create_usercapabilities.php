<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsercapabilities extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		
		Schema::create('capability_user', function(Blueprint $table)
		{
			$table->bigIncrements('id');

			$table->timestamps();

			$table->bigInteger('user_id')->unsigned();
			$table->integer('capability_id')->unsigned();

			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('capability_id')->references('id')->on('capabilities');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('capability_user');
	}

}
