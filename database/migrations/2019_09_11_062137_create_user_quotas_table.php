<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserQuotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_quotas', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->bigInteger('user_id')->unsigned();

            $table->integer('limit')->nullable()->default(null);
            $table->boolean('unlimited')->nullable()->default(null);
            $table->integer('used')->default(0);
            $table->integer('threshold')->nullable()->default(null);

            $table->timestamp('notification_sent_at')->nullable();
            $table->timestamp('full_notification_sent_at')->nullable();


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
        Schema::dropIfExists('user_quotas');
    }
}
