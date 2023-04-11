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
        Schema::create('eventtickets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('ticket_category_id');
            $table->bigInteger('event_id');
            $table->integer('ticket_price');
            $table->integer('no_of_ticket');
            $table->integer('available_tickets')->nullable();
            $table->integer('booked_tickets')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eventtickets');
    }
};