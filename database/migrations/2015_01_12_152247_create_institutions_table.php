<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Institution details cache table
 */
class CreateInstitutionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->bigIncrements('id');

            /**
             * Is the ID of the institution inside K-Link
             *
             * This must be in the json as "id"
             */
            $table->string('klink_id', 20)->unique();

            $table->string('name');

            $table->string('email');

            $table->string('phone')->nullable();

            $table->string('type');

            $table->string('address_street')->nullable(); //addressStreet

            $table->string('address_country')->nullable(); //addressCountry

            $table->string('address_locality')->nullable(); //addressLocality

            $table->string('address_zip')->nullable(); //addressZip

            /**
             * updated_at created_at
             */
            $table->timestamps();

            // created_at ==> creationDate on json

            $table->string('thumbnail_uri')->nullable();

            $table->string('url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('institutions');
    }
}
