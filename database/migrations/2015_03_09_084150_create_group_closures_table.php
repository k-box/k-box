<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupClosuresTable extends Migration
{
    public function up()
    {
        Schema::create('group_closure', function(Blueprint $table)
        {
            $table->increments('closure_id');

            $table->bigInteger('ancestor', false, true);
            $table->bigInteger('descendant', false, true);
            $table->integer('depth', false, true);

            $table->foreign('ancestor')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('descendant')->references('id')->on('groups')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('group_closure', function(Blueprint $table)
        {
            Schema::dropIfExists('group_closure');
        });
    }
}
