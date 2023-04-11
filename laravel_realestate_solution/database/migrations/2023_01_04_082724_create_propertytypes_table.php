<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('propertytypes', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('image');
            $table->integer('status')->nullable();
            $table->text('description');
            $table->integer('propertycategoryid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('propertytypes');
    }
};