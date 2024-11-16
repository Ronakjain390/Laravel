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
        Schema::table('company_logos', function (Blueprint $table) {
            $table->string('return_challan_logo_url')->nullable();
            $table->string('po_logo_url')->nullable();
            $table->enum('return_challan_alignment', ['left', 'right', 'center'])->nullable();
            $table->enum('po_alignment', ['left', 'right', 'center'])->nullable();
            $table->string('return_challan_heading')->nullable();
            $table->string('po_heading')->nullable();
            $table->boolean('return_challan_stamp')->nullable();
            $table->boolean('po_stamp')->nullable(); 
            $table->string('signature_sender')->nullable();
            $table->string('signature_receiver')->nullable();
            $table->string('signature_seller')->nullable();
            $table->string('signature_buyer')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_logos', function (Blueprint $table) {
            //
        });
    }
};
