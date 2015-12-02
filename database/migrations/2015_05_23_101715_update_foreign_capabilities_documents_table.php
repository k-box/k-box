<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateForeignCapabilitiesDocumentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Update capabilities <-> user pivot foreign keys
		
		Schema::table('capability_user', function(Blueprint $table)
		{
			$table->dropForeign('capability_user_user_id_foreign');
			$table->dropForeign('capability_user_capability_id_foreign');
			
			$table->foreign('user_id')->references('id')->unsigned()->on('users')->onDelete('cascade');
			$table->foreign('capability_id')->references('id')->unsigned()->on('capabilities')->onDelete('cascade');
		});
		
		// Update starred <-> descriptor foreign
		
		Schema::table('starred', function(Blueprint $table)
		{
			$table->dropForeign('starred_document_id_foreign');
			$table->dropForeign('starred_user_id_foreign');
			
			$table->foreign('user_id')->references('id')->on('users')->unsigned()->onDelete('cascade');
			$table->foreign('document_id')->references('id')->unsigned()->on('document_descriptors')->onDelete('cascade');
		});
		
		// Update document <-> groups foreign
		
		Schema::table('document_groups', function(Blueprint $table)
		{
			$table->dropForeign('document_groups_document_id_foreign');
			$table->dropForeign('document_groups_group_id_foreign');
			
			$table->foreign('document_id')->references('id')->on('document_descriptors')->unsigned()->onDelete('cascade');
			$table->foreign('group_id')->references('id')->on('groups')->unsigned()->onDelete('cascade');
		});
		
		
		Schema::table('import', function(Blueprint $table)
		{
			$table->dropForeign('import_file_id_foreign');
			$table->dropForeign('import_parent_id_foreign');
			$table->dropForeign('import_user_id_foreign');
			
			$table->foreign('user_id')->references('id')->on('users')->unsigned()->onDelete('cascade');
			$table->foreign('file_id')->references('id')->on('files')->unsigned()->onDelete('cascade');
			
			$table->foreign('parent_id')->references('id')->on('import')->unsigned()->onDelete('cascade'); 
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('capability_user', function(Blueprint $table)
		{
			$table->dropForeign('capability_user_user_id_foreign');
			$table->dropForeign('capability_user_capability_id_foreign');
			
			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('capability_id')->references('id')->on('capabilities');
		});
		
		Schema::table('starred', function(Blueprint $table)
		{
			$table->dropForeign('starred_document_id_foreign');
			$table->dropForeign('starred_user_id_foreign');
			
			$table->foreign('user_id')->references('id')->on('users');

			$table->foreign('document_id')->references('id')->on('document_descriptors');
			
		});
		
		Schema::table('document_groups', function(Blueprint $table)
		{
			$table->dropForeign('document_groups_document_id_foreign');
			$table->dropForeign('document_groups_group_id_foreign');
			
			$table->foreign('document_id')->references('id')->on('document_descriptors');

			$table->foreign('group_id')->references('id')->on('groups');
		});
		
		Schema::table('import', function(Blueprint $table)
		{
			$table->dropForeign('import_file_id_foreign');
			$table->dropForeign('import_parent_id_foreign');
			$table->dropForeign('import_user_id_foreign');
			
			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('file_id')->references('id')->on('files');
			$table->foreign('parent_id')->references('id')->on('import');
		});
		
	}

}
