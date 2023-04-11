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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('email');
            $table->string('contact_number', 15);
            $table->text('image');
            $table->tinyInteger('event_type');
            $table->bigInteger('category_id')->nullable();
            $table->bigInteger('foodstoreid')->nullable();
            $table->string('venue')->nullable();
            $table->float('price')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
            $table->integer('postal_code')->nullable();
            $table->integer('status');
            $table->text('about');
            $table->bigInteger('event_manager_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->tinyInteger('is_varified')->default(0);
            $table->timestamps();

            // $table->time('time')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('meeting_link')->nullable();
            $table->time('duration')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
};
