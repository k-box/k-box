<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSharedTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shared', function (Blueprint $table) {
            $table->dropForeign('shared_shared_with_foreign');
            $table->dropIndex('shared_shared_with_index');
            $table->dropUnique('shared_token_shared_with_shareable_id_shareable_type_unique');
            
            $table->dropColumn('shared_with');
            
            $table->bigInteger('with_id')->unsigned();
            $table->string('with_type');

            
            $table->unique(['token', 'with_id', 'with_type']);

            $table->index(['with_id', 'with_type']);
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
            //			$table->dropForeign('shared_shared_with_foreign');
            
            $table->dropColumn('with_id');
            $table->dropColumn('with_type');
            
            $table->bigInteger('shared_with')->unsigned();
            $table->foreign('shared_with')->references('id')->on('users');
            
            $table->unique(['token', 'shared_with', 'shareable_id', 'shareable_type']);
        });
    }
}
