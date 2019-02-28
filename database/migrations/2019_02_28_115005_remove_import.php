<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveImport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop import table and migrations that created it
        Schema::dropIfExists('import');

        $migrations = [
            '2015_02_10_181956_imported_file',
            '2015_02_11_053403_imported_file_status_message',
            '2015_02_11_070812_imported_file_child_id',
            '2015_02_12_082701_import_is_remote',
            '2016_02_01_083232_update_import_error_handling',
            '2016_05_05_131917_add_import_job_payload',
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
