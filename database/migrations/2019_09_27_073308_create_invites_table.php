<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->index();
            $table->timestamps();
            $table->bigInteger('creator_id')->unsigned();
            $table->string('token', 100)->unique();
            $table->string('email')->unique();
            $table->dateTime('accepted_at')->nullable()->index();
            $table->dateTime('expire_at')->index();
            $table->json('details')->nullable();
            $table->bigInteger('user_id')->nullable()->unsigned();
            $table->nullableMorphs('actionable'); // represent the action to be performed on the referenced model upon user registration

            $table->foreign('creator_id')
                ->references('id')->on('users')
                ->onDelete('cascade');            
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invites');
    }
}
