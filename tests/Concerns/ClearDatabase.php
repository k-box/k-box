<?php

namespace Tests\Concerns;

use DB;
use Schema;

trait ClearDatabase
{
    /**
     * Empty the main tables of the K-Box to ensure that other tests did
     * not left entries
     */
    public function clearDatabase()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('document_descriptors')->truncate();
        DB::table('files')->truncate();
        DB::table('capability_user')->truncate();
        DB::table('users')->truncate();
        DB::table('user_options')->truncate();
        DB::table('publications')->truncate();
        DB::table('projects')->truncate();
        DB::table('userprojects')->truncate();
        DB::table('groups')->truncate();
        DB::table('group_closure')->truncate();
        DB::table('document_groups')->truncate();
        DB::table('shared')->truncate();
        DB::table('options')->truncate();
        Schema::enableForeignKeyConstraints();
    }
}
