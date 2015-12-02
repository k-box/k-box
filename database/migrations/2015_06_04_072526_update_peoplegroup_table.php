<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePeoplegroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('peoplegroup', function(Blueprint $table)
		{
			$table->boolean('is_institution_group')->default(false);
			
			$table->index('is_institution_group');
			
			$table->index('name');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('peoplegroup', function(Blueprint $table)
		{
			$table->dropColumn('is_institution_group');
			
			$table->dropIndex('peoplegroup_name_index');
			$table->dropIndex('peoplegroup_is_institution_group_index');
		});
	}

}
