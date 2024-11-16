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
        Schema::create('product_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('product')->onDelete('cascade');
            $table->foreignId('challan_id')->constrained('challans')->onDelete('cascade');  
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('qty_out');
            $table->string('out_method');
            $table->timestamp('out_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_logs');
    }
};
