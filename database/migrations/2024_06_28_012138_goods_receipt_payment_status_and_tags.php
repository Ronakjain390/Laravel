<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags_table', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('panel_id'); // Ensure this matches the type in 'panel' table
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('panel_id')->references('id')->on('panel')->onDelete('cascade'); // Correct table name to 'panel'
        });

        Schema::create('payment_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('panel_id'); // Ensure this matches the type in 'panel' table
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('panel_id')->references('id')->on('panel')->onDelete('cascade'); // Correct table name to 'panel'
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tags_table');
        Schema::dropIfExists('payment_statuses');
    }
};