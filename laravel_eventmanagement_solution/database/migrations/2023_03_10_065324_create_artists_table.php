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
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nick_name')->nullable();
            $table->string('email');
            $table->string('contact_number', 15);
            $table->text('bio');
            $table->text('country');
            $table->text('image');
            $table->text('gallary_images')->nullable();
            $table->text('video')->nullable();
            $table->text('user_id')->nullable();
            $table->integer('type')->nullable();
            $table->text('expertize')->nullable();
            $table->integer('status');
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
        Schema::dropIfExists('artists');
    }
};
