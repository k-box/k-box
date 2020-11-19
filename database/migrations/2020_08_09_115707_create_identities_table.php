<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdentitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('identities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();  // the local user identifier
            $table->string('provider')->index();    // the authentication provider name
            $table->string('provider_id')->index(); // the user identifier within the authentication service
            $table->mediumText('token');
            $table->mediumText('refresh_token')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->boolean('registration')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('identities');
    }
}
