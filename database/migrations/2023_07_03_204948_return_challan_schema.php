<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('return_challans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challan_id');
            $table->string('challan_series');
            $table->string('series_num');
            $table->unsignedBigInteger('sender_id');
            $table->string('sender');
            $table->unsignedBigInteger('receiver_id');
            $table->string('receiver');
            $table->string('pdf_url')->nullable();
            $table->text('comment')->nullable();
            $table->json('status_comment')->nullable();
            $table->decimal('total', 20, 2)->default(0.00);
            $table->date('challan_date');
            $table->softDeletes();
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('challan_id')->references('id')->on('challans')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users');
            $table->foreign('receiver_id')->references('id')->on('users');
        });

        Schema::create('return_challan_order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challan_id');
            $table->string('unit');
            $table->decimal('rate', 20, 2)->default(0.00);
            $table->integer('qty');
            $table->decimal('total_amount', 20, 2)->default(0.00);
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('challan_id')->references('id')->on('return_challans')->onDelete('cascade');
        });

        Schema::create('return_challan_order_columns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challan_order_detail_id');
            $table->string('column_name');
            $table->string('column_value');
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('challan_order_detail_id')->references('id')->on('return_challan_order_details')->onDelete('cascade');
        });

        Schema::create('return_challan_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challan_id');
            $table->unsignedBigInteger('user_id');
            $table->string('user_name');
            $table->enum('status', ['draft', 'modified', 'sent', 'resent', 'accepted', 'rejected','self-accepted', 'self_reject','deleted']);
            $table->text('comment')->nullable();
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('challan_id')->references('id')->on('return_challans')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
        });


        Schema::create('return_challan_sfp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challan_id');
            $table->unsignedBigInteger('sfp_by_id');
            $table->string('sfp_by_name');
            $table->unsignedBigInteger('sfp_to_id')->nullable();
            $table->string('sfp_to_name')->nullable();
            $table->text('comment')->nullable();
            $table->enum('status', ['accept', 'reject', 'sent']);
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('challan_id')->references('id')->on('return_challans')->onDelete('cascade');
            $table->foreign('sfp_by_id')->references('id')->on('users');
            $table->foreign('sfp_to_id')->references('id')->on('users');
        });

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_challans');
        Schema::dropIfExists('return_challan_order_details');
        Schema::dropIfExists('return_challan_order_columns');
        Schema::dropIfExists('return_challan_statuses');
        Schema::dropIfExists('return_challan_sfp');
    }

};
