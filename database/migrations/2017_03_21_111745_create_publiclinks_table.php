<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publiclinks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            // to store who created it
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            // to store a human friendly version to be used to identify the document
            // in theory the same slug cannot be applied to two different share
            $table->string('slug', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('publiclinks');
    }
}
