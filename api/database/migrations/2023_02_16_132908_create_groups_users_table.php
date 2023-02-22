<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups_users', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('group_id')->unsigned();
            $table->smallInteger('role_id');
            $table->timestamps();

            $table->primary(['user_id', 'group_id']);

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('restrict')
                ->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')
                ->onUpdate('restrict')
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
        Schema::dropIfExists('groups_users');
    }
};
