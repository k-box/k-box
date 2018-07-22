<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFilesToLinkWithTusUpload extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            // the connection to the tus_upload_queue table created by the laravel-tus-upload package
            $table->string('request_id')->nullable();

            // this is a duplication of the upload queue fields, as the entry in the upload queue
            // will be cleaned by cron jobs
            // when the upload was started by the user
            $table->timestamp('upload_started_at')->nullable();

            // when the upload has been cancelled by the user
            $table->timestamp('upload_cancelled_at')->nullable();

            // when the upload completed
            $table->timestamp('upload_completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('request_id');
            $table->dropColumn('upload_started_at');
            $table->dropColumn('upload_cancelled_at');
            $table->dropColumn('upload_completed_at');
        });
    }
}
