<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->string('item_code');
            $table->string('category')->nullable();
            $table->string('warehouse')->nullable();
            $table->string('location')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('rate',20, 2)->default(0.00);
            $table->integer('qty')->default(0);
            $table->decimal('total_amount',20, 2)->default(0.00);
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
        Schema::create('product_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('column_name');
            $table->string('column_value');
            $table->timestamps();

            // Define foreign key constraint for
            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_detail');
    }
};
