<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GeneralUpgradeInPreparationForSearchApiChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('microsites', function (Blueprint $table) {
            $table->dropForeign('microsites_institution_id_foreign');
            $table->dropColumn('institution_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('microsites', function (Blueprint $table) {
            // this will not restore the data, but at least redefines 
            // the table and the foreign key
            $table->bigInteger('institution_id')->nullable();
            $table->foreign('institution_id')->references('id')->on('institutions');
        });
    }
}
