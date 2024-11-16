<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('goods_receipt_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goods_receipt_id');
            $table->unsignedBigInteger('tags_table_id');
            $table->unsignedBigInteger('user_id');
            $table->string('table_id');
            $table->unsignedInteger('panel_id');
            $table->timestamps();

            $table->foreign('goods_receipt_id')->references('id')->on('goods_receipts')->onDelete('cascade');
            $table->foreign('tags_table_id')->references('id')->on('tags_table')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('panel_id')->references('id')->on('panel')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('goods_receipt_tags');
    }
};
