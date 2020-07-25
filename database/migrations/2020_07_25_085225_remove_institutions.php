<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveInstitutions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop institutions related tables and migrations
        if(Schema::hasColumn('users', 'institution_id')){
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign('users_institution_id_foreign');
                $table->dropColumn('institution_id');
            });
        }

        if(Schema::hasColumn('document_descriptors', 'institution_id')){
            Schema::table('document_descriptors', function (Blueprint $table) {
                $table->dropForeign('document_descriptors_institution_id_foreign');
                $table->dropColumn('institution_id');
            });
        }

        Schema::dropIfExists('institutions');

        $migrations = [
            '2015_01_12_152247_create_institutions_table',
            '2015_07_29_055028_update_user_with_institution',
        ];

        DB::table('migrations')->whereIn('migration', $migrations)->delete();
    }

    public function down() {}
}
