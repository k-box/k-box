<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProjectUniqueness extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique('projects_name_unique');
            $table->unique(['user_id', 'name'], 'projects_name_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            // required to drop foreign key before the unique index
            // as the index used a column on which a foreign key was
            // defined
            $table->dropForeign('projects_user_id_foreign');

            $table->dropUnique('projects_name_user_unique');
            $table->unique('name');

            // restore the foreign key afterwards
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
