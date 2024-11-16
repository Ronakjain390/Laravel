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
        Schema::create('return_challan_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challan_id');
            $table->unsignedBigInteger('tags_table_id');
            $table->unsignedBigInteger('user_id');
            $table->string('table_id');
            $table->unsignedInteger('panel_id');
            $table->timestamps();

            $table->foreign('challan_id')->references('id')->on('return_challans')->onDelete('cascade');
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
        DropIfExists('return_challan_tags');
    }
};
