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
    public function up(): void
    {
        Schema::create('team_user_permission', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_user_id');
            $table->unsignedInteger('team_id');
            $table->unsignedBigInteger('team_owner_user_id');
            $table->string('permission');
            $table->enum('status', ['active', 'pause', 'terminated']);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));

            $table->foreign('team_user_id')->references('id')->on('team_users');
            $table->foreign('team_owner_user_id')->references('id')->on('users');
            $table->foreign('team_id')->references('id')->on('teams');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('team_user_permission');

    }
};
