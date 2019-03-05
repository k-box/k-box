<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemovePeoplegroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop import table and migrations that created it
        Schema::dropIfExists('peoplegroup_to_user');
        Schema::dropIfExists('peoplegroup');

        $migrations = [
            '2015_05_31_151323_create_people_group_table',
            '2015_05_31_153348_create_peoplegroup_to_user_table',
            '2015_06_04_072526_update_peoplegroup_table',
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
