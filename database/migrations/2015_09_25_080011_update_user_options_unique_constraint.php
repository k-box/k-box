<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserOptionsUniqueConstraint extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_options', function(Blueprint $table)
		{
			// Drop the old uniqueness contraint only on the key column
			$table->dropUnique('user_options_key_unique');
			
			// Add the new uniqueness contraint to [key, user_id] columns
			$table->unique(['key', 'user_id']);
			
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_options', function(Blueprint $table)
		{
			
			$table->dropUnique('user_options_key_user_id_unique');
			
			$table->unique('key');
			
		});
	}

}
