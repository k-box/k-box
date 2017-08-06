<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSharedWithTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shared', function (Blueprint $table) {
            $table->dropIndex('shared_with_id_with_type_index');
            $table->dropUnique('shared_token_with_id_with_type_unique');
            
            
            $table->dropColumn('with_id');
            $table->dropColumn('with_type');
            
            $table->bigInteger('sharedwith_id')->unsigned();
            $table->string('sharedwith_type');
            
            $table->index(['sharedwith_id', 'sharedwith_type']);
            
            $table->unique(['token', 'sharedwith_id', 'sharedwith_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shared', function (Blueprint $table) {
            $table->dropUnique('shared_token_sharedwith_id_sharedwith_type_unique');
            $table->dropIndex('shared_shareable_id_shareable_type_index');
            
            $table->dropColumn('sharedwith_id');
            $table->dropColumn('sharedwith_type');
            
            $table->bigInteger('with_id')->unsigned();
            $table->string('with_type');
            
            $table->index(['with_id', 'with_type']);
            
            $table->unique(['token', 'with_id', 'with_type']);
        });
    }
}
