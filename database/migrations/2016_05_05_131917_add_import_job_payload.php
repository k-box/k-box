<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImportJobPayload extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('import', function (Blueprint $table) {
        //     // the original payload of the Job to enable an import retry
        //     $table->text('job_payload')->nullable();
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
