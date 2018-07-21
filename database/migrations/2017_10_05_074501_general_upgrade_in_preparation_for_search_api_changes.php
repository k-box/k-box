<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;

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
            try {
                $table->dropForeign('microsites_institution_id_foreign');
            } catch (QueryException $ex) {
            }
                
            $table->dropColumn('institution_id');
        });
        
        Schema::table('files', function (Blueprint $table) {
            $table->text('properties')->nullable();
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
            try {
                $table->foreign('institution_id')->references('id')->on('institutions');
            } catch (QueryException $ex) {
            }
        });

        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('properties');
        });
    }
}
