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
        Schema::create('food_stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('contact_number', 15);
            $table->text('description');
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->Integer('postal_code')->nullable();
            $table->text('image');
            $table->text('video')->nullable();
            $table->integer('status');
            $table->float('longitude')->nullable();
            $table->float('latitude')->nullable();
            $table->text('gallary_images')->nullable();
            $table->unsignedBigInteger('itemid')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('type')->nullable();
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
        Schema::dropIfExists('food_stores');
    }
};
