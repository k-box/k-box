<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateImportErrorHandling extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('import', function (Blueprint $table) {
            
            // Stores the message to be showed to the user
            $table->mediumText('message')->nullable();
            
            // Stores the detailed error or information payload if available
            $table->json('payload')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('import', function (Blueprint $table) {
            
            $table->dropColumn('message');
            $table->dropColumn('payload');
            
        });
    }
}
