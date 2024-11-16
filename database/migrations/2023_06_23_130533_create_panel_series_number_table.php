<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('panel_series_numbers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('series_number');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('panel_id');
            $table->unsignedInteger('section_id');
            $table->unsignedInteger('assigned_to_id')->nullable();
            $table->string('assigned_to_name')->nullable();
            $table->enum('status', ['active', 'pause', 'terminated'])->default('active');
            $table->date('valid_from');
            $table->date('valid_till');
            $table->enum('default', ['0', '1'])->default('0');
            $table->timestamps();
        
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('panel_id')->references('id')->on('panel');
            $table->foreign('section_id')->references('id')->on('section');
        });
        
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panel_series_numbers');
    }
};
