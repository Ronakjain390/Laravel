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
        Schema::create('purchase_order_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order');
            $table->unsignedBigInteger('tags_table_id');
            $table->unsignedBigInteger('user_id');
            $table->string('table_id');
            $table->unsignedInteger('panel_id');
            $table->timestamps();

            $table->foreign('purchase_order')->references('id')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('tags_table_id')->references('id')->on('tags_table')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('panel_id')->references('id')->on('panel')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order');
    }
};
