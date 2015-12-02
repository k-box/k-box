<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImportedFileChildId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('import', function(Blueprint $table)
		{
                        $table->foreign('parent_id')->references('id')->on('import'); 
                        $table->bigInteger('parent_id')->unsigned()->nullable(); 
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('import', function(Blueprint $table)
		{
			$table->dropColumn('parent_id');
		});
	}
}
