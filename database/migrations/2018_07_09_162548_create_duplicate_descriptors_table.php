<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDuplicateDescriptorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('duplicate_descriptors', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            // the newly uploaded document, that is a duplicate
            $table->bigInteger('duplicate_document_id')->unsigned();

            // the document document that was already in the system
            $table->bigInteger('document_id')->unsigned();

            // When the user has been notified about the duplicate
            $table->timestamp('notification_sent_at')->nullable();
            
            // The user that uploaded the duplicate entry
            $table->bigInteger('user_id')->nullable()->unsigned();
            
            $table->foreign('user_id')->references('id')->on('users');
            
            $table->foreign('document_id')->references('id')->on('document_descriptors');
            $table->foreign('duplicate_document_id')->references('id')->on('document_descriptors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('duplicate_descriptors');
    }
}
