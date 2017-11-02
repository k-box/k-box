<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publications', function (Blueprint $table) {
            $table->bigIncrements('id');
            // the target network where the publication happens
            $table->string('network')->nullable();
            // create and update date, creation date means when the publishing operation was started 
            $table->timestamps();

            $table->bigInteger('descriptor_id')->unsigned();
            $table->boolean('pending')->default(true);

            // who triggered the publishing operation and when
            $table->bigInteger('published_by')->unsigned()->nullable();
            $table->timestamp('published_at')->nullable();
            
            // who triggered the un-publishing operation and when
            $table->bigInteger('unpublished_by')->unsigned()->nullable();
            $table->timestamp('unpublished_at')->nullable();
            
            // in case a failure happens, this enable to tell the user that the document is not on the network
            $table->timestamp('failed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('published_documents');
    }
}
