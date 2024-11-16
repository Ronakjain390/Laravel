<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('buyers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('buyer_user_id')->nullable();
            $table->string('buyer_name');
            $table->enum('status', ['active', 'pause', 'terminate'])->nullable();
            $table->string('buyer_special_id')->nullable(); // New column
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('buyer_user_id')->references('id')->on('users');
            $table->foreign('buyer_special_id')->references('special_id')->on('users');
        });

        Schema::create('buyer_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('buyer_id');
            $table->text('address')->nullable();
            $table->integer('pincode')->nullable();
            $table->string('phone', 191)->nullable();
            $table->string('gst_number', 191)->nullable();
            $table->string('state', 75)->nullable();
            $table->string('city', 75)->nullable();
            $table->string('bank_name', 255)->nullable();
            $table->string('branch_name', 255)->nullable();
            $table->string('bank_account_no', 255)->nullable();
            $table->string('ifsc_code', 255)->nullable();
            $table->string('tan', 15)->nullable();
            $table->enum('status', ['active', 'pause', 'terminate'])->nullable();
            $table->timestamps();

            $table->foreign('buyer_id')->references('id')->on('buyers');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('buyers');

    }
};
