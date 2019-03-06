<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveNavigationMemories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop import table and migrations that created it
        Schema::dropIfExists('request_search_query_activities');
        Schema::dropIfExists('open_document_activities');
        Schema::dropIfExists('memory_activities');
        Schema::dropIfExists('memory_memories');
        Schema::dropIfExists('navigation_memories');
        Schema::dropIfExists('activities');

        $migrations = [
            '2015_01_13_104546_create_activities_table',
            '2015_01_13_130654_create_memory_memories_table',
            '2015_01_13_105336_create_memory_activities_table',
            '2015_01_13_104554_create_navigation_memories_table',
            '2015_01_13_105515_create_open_document_activities_table',
            '2015_01_13_105502_create_request_search_query_activities_table',
        ];

        DB::table('migrations')->whereIn('migration', $migrations)->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
}
