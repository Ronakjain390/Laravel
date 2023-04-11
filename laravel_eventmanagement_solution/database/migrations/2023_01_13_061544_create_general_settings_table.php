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
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('logo')->nullable();
            $table->text('favicon');
            $table->text('logo_mini');
            $table->text('copyright_text');
            $table->text('address');
            $table->string('country');
            $table->string('state');
            $table->integer('zipCode');
            $table->float('longitude')->nullable();
            $table->float('latitude')->nullable();
            $table->string('city');
            $table->string('email');
            $table->string('title');
            $table->string('date_format');
            $table->bigInteger('contact_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_settings');
    }
};
