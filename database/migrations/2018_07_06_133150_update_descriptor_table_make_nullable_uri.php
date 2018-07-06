<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDescriptorTableMakeNullableUri extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_descriptors', function (Blueprint $table) {
            $table->string('document_uri')->nullable()->change();
            $table->string('thumbnail_uri')->nullable()->change();
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
            $table->string('document_uri')->change();
            $table->string('thumbnail_uri')->change();
        });
    }
}
