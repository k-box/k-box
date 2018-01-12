<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDocumentDescriptorsWithCopyrightFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_descriptors', function (Blueprint $table) {
            $table->string('copyright_usage')->nullable();
            $table->text('copyright_owner')->nullable();
            // the copyright_owner field will be considered a serialized object containing name, website, email, address
            // using text as json data type is not yet supported on MariaDB

            $table->index('copyright_usage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_descriptors', function (Blueprint $table) {
            $table->dropColumn('copyright_usage');
            $table->dropColumn('copyright_owner');
        });
    }
}
