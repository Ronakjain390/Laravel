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
        Schema::create('challan_delivery_statuses', function (Blueprint $table) {
            $table->unsignedBigInteger('challan_id');
            $table->unsignedBigInteger('challan_deliveries_id');
            $table->primary(['challan_id', 'challan_deliveries']);
            $table->foreign('challan_id')->references('id')->on('challans')->onDelete('cascade');
            $table->foreign('challan_deliveries_id')->references('id')->on('challan_deliveries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challan_delivery_statuses');
    }
};
