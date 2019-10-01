<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserToDocumentGroupsPivot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_groups', function (Blueprint $table) {
            
            $table->bigInteger('added_by')->unsigned()->nullable();

            $table->nullableTimestamps();

            $table->foreign('added_by')
                ->references('id')
                ->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_groups', function (Blueprint $table) {
            $table->dropForeign('document_groups_added_by_foreign');
            $table->dropColumn('added_by');
            $table->dropTimestamps();
        });
    }
}
