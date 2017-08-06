<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('user_id')->unsigned();

            /**
             * The group name
             */
            $table->string('name');

            /**
             * updated_at created_at
             */
            $table->timestamps();

            /**
             * Undo your delete.
             *
             * use Illuminate\Database\Eloquent\SoftDeletes;
             *
             * class User extends Eloquent {
             *
             *    use SoftDeletes;
             *
             * 	  protected $dates = ['deleted_at'];
             *
             * 	//...
             * 	}
             *
             */
            $table->softDeletes();

            /**
             * The color of the group.
             * Hex color without the sharp
             */
            $table->string('color', 6)->nullable();

            /**
             * Tell if the group is private to the user.
             *
             * If the group is public the user_id field contain
             * the id of the user that has created the group
             */
            $table->string('is_private')->default(true);

            /**
             * The type of the group
             */
            $table->integer('group_type_id')->unsigned();

            /**
             * Handle Hierarchy with Closure Table pattern
             */
            $table->bigInteger('parent_id')->unsigned()->nullable();
            $table->integer('position', false, true);
            $table->integer('real_depth', false, true);

            
                        /**
             * The group must be unique for name user and parent group.
             *
             *     b*
             *    /
             *   a
             *  / \
             * x   b*
             *  \
             *   b
             *
             * the b* must exists single!
             */
            $table->unique(['user_id', 'name', 'parent_id', 'is_private']);

            $table->foreign('user_id')->references('id')->on('users');

            $table->foreign('group_type_id')->references('id')->on('group_types');

            $table->foreign('parent_id')->references('id')->on('groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
}
