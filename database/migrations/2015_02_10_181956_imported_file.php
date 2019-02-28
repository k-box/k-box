<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImportedFile extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('import', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->bigInteger('bytes_expected')->unsigned();
        //     $table->bigInteger('bytes_received')->unsigned();
        //     $table->bigInteger('user_id')->unsigned();
        //     $table->bigInteger('file_id')->unsigned();
        //     $table->timestamps();
        //     $table->integer('status');
        //     $table->unique(['user_id', 'file_id']);
        //     $table->foreign('user_id')->references('id')->on('users');
        //     $table->foreign('file_id')->references('id')->on('files');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // this is kept for compatibility with existing
        // deployments migrations
        Schema::dropIfExists('import');
    }
}
