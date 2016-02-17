<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFilesPathsLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('files', function (Blueprint $table) {
            $table->dropForeign('files_user_id_foreign');
            $table->dropUnique('files_user_id_name_path_unique');
        });
        
        Schema::table('files', function (Blueprint $table) {
                        
            $table->mediumText('path')->change();
            $table->mediumText('original_uri')->change();
            $table->mediumText('thumbnail_path')->change();
            
            $table->foreign('user_id')->references('id')->on('users');
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
            
            $table->string('path', 255)->change();
            $table->string('original_uri', 255)->change();
            $table->string('thumbnail_path', 255)->change();
            
            $table->unique(array('user_id', 'name', 'path'));
        });
    }
}
