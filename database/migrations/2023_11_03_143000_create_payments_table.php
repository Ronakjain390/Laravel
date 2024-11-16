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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->string('order_id')->nullable();
            $table->string('razorpay_payment_id')->nullable();
            $table->boolean('payment_done')->default(false);
    
            $table->foreign('user_id')->references('id')->on('users');
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
