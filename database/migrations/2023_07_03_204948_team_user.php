<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('team_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('team_user_name');
            $table->string('team_name');
            $table->unsignedBigInteger('team_user_id')->nullable();
            $table->string('email', 191)->nullable();
            $table->string('password', 191)->nullable();
            $table->longText('team_user_address')->nullable();
            $table->integer('team_user_pincode')->nullable();
            $table->string('phone', 191);
            $table->string('team_user_state', 75)->nullable();
            $table->string('team_user_city', 75)->nullable();
            $table->unsignedInteger('team_id');
            $table->unsignedBigInteger('team_owner_user_id');
            $table->enum('status', ['active', 'pause', 'terminated']);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));

            $table->foreign('team_user_id')->references('id')->on('users');
            $table->foreign('team_id')->references('id')->on('teams');
            $table->foreign('team_owner_user_id')->references('id')->on('users');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('return_challans');
        Schema::dropIfExists('team_users');
    }
};
