<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('series_number');
            $table->unsignedBigInteger('seller_id');
            $table->string('seller_name');
            $table->unsignedBigInteger('buyer_id');
            $table->string('buyer_name');
            $table->string('pdf_url')->nullable();
            $table->text('comment')->nullable();
            $table->json('status_comment')->nullable();
            $table->decimal('total', 20, 2)->default(0.00);
            $table->softDeletes();
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('seller_id')->references('id')->on('users');
            $table->foreign('buyer_id')->references('id')->on('users');
        });

        Schema::create('purchase_order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id');
            $table->string('unit');
            $table->decimal('rate', 20, 2)->default(0.00);
            $table->decimal('tax', 20, 2)->default(0.00);
            $table->integer('qty');
            $table->decimal('total_amount', 20, 2)->default(0.00);
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
        });

        Schema::create('purchase_order_columns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_detail_id');
            $table->string('column_name');
            $table->string('column_value');
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('purchase_order_detail_id')->references('id')->on('purchase_order_details')->onDelete('cascade');
        });

        Schema::create('purchase_order_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('user_id');
            $table->string('user_name');
            $table->enum('status', ['draft', 'modified', 'sent', 'resent', 'accepted', 'rejected','self-accepted', 'self_reject','deleted']);
            $table->text('comment')->nullable();
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
        });


        Schema::create('purchase_order_sfp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('sfp_by_id');
            $table->string('sfp_by_name');
            $table->unsignedBigInteger('sfp_to_id');
            $table->string('sfp_to_name');
            $table->text('comment')->nullable();
            $table->enum('status', ['accepted', 'rejected']);
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('sfp_by_id')->references('id')->on('users');
            $table->foreign('sfp_to_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
