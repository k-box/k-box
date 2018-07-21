<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentDescriptorsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_descriptors', function (Blueprint $table) {
            $table->bigIncrements('id'); // in K-Link is the localDocumentId

            $table->bigInteger('institution_id')->nullable()->unsigned(); //institutionID

            $table->string('local_document_id')->nullable(); //Klink Local document id

            $table->string('hash', 128);

            $table->string('title');

            $table->string('document_uri'); //documentUri

            $table->string('thumbnail_uri'); //thumbnailUri

            $table->string('mime_type'); //mimeType

            $table->string('visibility', 10);

            $table->string('document_type')->default('document'); //documentType

            /**
             * The owner of the document.
             *
             * If the descriptor describes a remote document this will be filled.
             *
             * Otherwise the owner is the user referenced the {owner_id} field
             */
            $table->string('user_owner')->nullable(); //userOwner

            /**
             * Stores the user that has uploaded the document.
             *
             * If the descriptor describes a remote document this will be filled.
             *
             * Otherwise the uploader is the user that has uploaded the file
             */
            $table->string('user_uploader')->nullable(); //userUploader

            // userOwner
            //
            //
            // userUploader is a nullable string, if null and document is local the uploader is the File::user_id

            $table->mediumText('abstract')->nullable();

            $table->string('language', 10)->nullable();

            $table->string('authors')->nullable(); //array of “Name Surname <mail@host.com>”

            /**
             * Undo your delete.
             *
             * use Illuminate\Database\Eloquent\SoftDeletes;
             *
             * class User extends Eloquent {
             *
             *    use SoftDeletes;
             *
             *    protected $dates = ['deleted_at'];
             *
             *  //...
             *  }
             *
             */
            $table->softDeletes();

            /**
             * updated_at created_at
             */
            $table->timestamps();

            // created_at ==> creationDate on json

            $table->bigInteger('file_id')->nullable()->unsigned();

            $table->bigInteger('owner_id')->nullable()->unsigned();

            
            $table->foreign('file_id')->references('id')->on('files');

            $table->foreign('owner_id')->references('id')->on('users');

            $table->foreign('institution_id')->references('id')->on('institutions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_descriptors');
    }
}
