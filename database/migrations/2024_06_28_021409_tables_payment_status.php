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
        Schema::create('goods_receipt_payment_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goods_receipt_id');
            $table->unsignedBigInteger('payment_status_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('panel_id');
            $table->timestamps();

            $table->foreign('goods_receipt_id')->references('id')->on('goods_receipts')->onDelete('cascade');
            $table->foreign('payment_status_id')->references('id')->on('payment_statuses')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('panel_id')->references('id')->on('panel')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_payment_status');
    }
};
