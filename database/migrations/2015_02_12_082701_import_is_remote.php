<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImportIsRemote extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('import', function (Blueprint $table) {
        //     $table->boolean('is_remote');
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
