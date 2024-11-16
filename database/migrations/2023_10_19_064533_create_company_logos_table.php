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
        Schema::create('company_logos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('section_id');
            $table->string('invoice_logo_url')->nullable();
            $table->string('challan_logo_url')->nullable();
            $table->enum('challan_alignment', ['left', 'right', 'center'])->nullable();
            $table->enum('invoice_alignment', ['left', 'right', 'center'])->nullable();
            $table->string('challan_heading')->nullable();
            $table->string('invoice_heading')->nullable();
            $table->boolean('challan_stamp')->nullable();
            $table->boolean('invoice_stamp')->nullable();
            $table->boolean('barcode_accept')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('section_id')->references('id')->on('section');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_logos');
    }
};
