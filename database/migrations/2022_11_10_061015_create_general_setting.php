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
        Schema::create('general_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('logo');
            $table->text('favicon');
            $table->text('logo_mini');
            $table->text('copyrightText');
            $table->text('address');
            $table->string('country');
            $table->string('state');
            $table->integer('zipCode');
            $table->integer('longitude');
            $table->integer('latitude');
            $table->string('city');
            $table->string('email');
            $table->string('title');
            $table->string('dateFormat');
            $table->bigInteger('contactNumber');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_setting');
    }
};
