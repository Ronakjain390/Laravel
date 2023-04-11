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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->integer('userId')->nullable();
            $table->timestamps();
            $table->text('name');
            $table->string('email');
            $table->bigInteger('contactNumber');
            $table->text('title');
            $table->text('Utype');
            $table->text('reason');
            $table->text('address');
            $table->integer('builtArea');
            $table->integer('carpetArea');
            $table->integer('bedroom');
            $table->integer('bathroom');
            $table->text('priceType');
            $table->bigInteger('totalPrice');
            $table->text('description')->nullable();
            $table->text('generalAmenities');
            $table->float('longitude')->nullable();
            $table->float('latitude')->nullable();
            $table->text('city');
            $table->text('state');
            $table->text('country');
            $table->text('image');
            $table->integer('status')->nullable();
            $table->bigInteger('postal_code');
            $table->integer('agentid')->nullable();
            $table->integer('ptype')->nullable();
            $table->integer('propertycategory')->nullable();
            $table->integer('propertyflag')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('properties');
    }
};