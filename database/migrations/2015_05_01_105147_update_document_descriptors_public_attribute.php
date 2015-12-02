<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDocumentDescriptorsPublicAttribute extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('document_descriptors', function(Blueprint $table)
		{
			$table->boolean('is_public')->default(false);

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('document_descriptors', function(Blueprint $table)
		{
			$table->dropColumn('is_public');
		});
	}

}
