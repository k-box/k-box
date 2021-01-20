<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use KBox\Group;

class UpdateGroupTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign("groups_group_type_id_foreign");
            $table->dropColumn("group_type_id");

            $table->unsignedInteger('type')->default(Group::TYPE_PERSONAL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->integer('group_type_id')->unsigned()->nullable();

            $table->foreign('group_type_id')->references('id')->on('group_types');

            $table->dropColumn('type');
        });
    }
}
