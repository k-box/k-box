<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use KBox\Flags;
use KBox\Option;

class RemoveMicrosites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('microsite_contents');
        Schema::dropIfExists('microsites');

        $migrations = [
            '2016_02_12_113414_create_microsites_content_table',
            '2016_02_12_113407_create_microsites_table',
        ];

        DB::table('migrations')->whereIn('migration', $migrations)->delete();

        Option::remove('flag_microsites');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() { }
}
