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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_series');
            $table->string('series_num');
            $table->unsignedBigInteger('seller_id');
            $table->string('seller');
            $table->unsignedBigInteger('buyer_id');
            $table->string('buyer');
            $table->string('pdf_url')->nullable();
            $table->text('comment')->nullable();
            $table->json('status_comment')->nullable();
            $table->boolean('calculate_tax')->default(true);
            $table->decimal('total', 20, 2)->default(0.00);
            $table->biginteger('buyer_detail_id')->nullable();
            $table->date('invoice_date');
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('seller_id')->references('id')->on('users');
            $table->foreign('buyer_id')->references('id')->on('users');
        });

        Schema::create('invoice_order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->string('unit')->nullable();
            $table->decimal('rate',20, 2)->default(0.00)->nullable();
            $table->integer('qty')->default(0)->nullable();
            $table->string('details')->nullable()->nullable();
            $table->decimal('tax', 20,2)->default(0.00)->nullable();
            $table->decimal('discount', 20,2)->default(0.00)->nullable();
            $table->decimal('total_amount',20, 2)->default(0.00)->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });
        Schema::create('invoice_order_columns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_order_detail_id');
            $table->string('column_name')->nullable();
            $table->string('column_value')->nullable();
            $table->timestamps();

            // Define foreign key constraint for invoice_order_detail_id
            $table->foreign('invoice_order_detail_id')->references('id')->on('invoice_order_details')->onDelete('cascade');
        });
        Schema::create('invoice_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('user_id');
            $table->string('user_name');
            $table->string('team_user_name');
            $table->enum('status', ['draft', 'modified', 'sent', 'resent', 'accept', 'reject','self_accept','partially_self_return', 'self_return', 'self_reject','deleted']);
            $table->text('comment')->nullable();
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('invoice_sfp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('sfp_by_id');
            $table->string('sfp_by_name');
            $table->unsignedBigInteger('sfp_to_id')->nullable();
            $table->string('sfp_to_name')->nullable();
            $table->text('comment')->nullable();
            $table->enum('status', ['accept', 'reject', 'sent'])->nullable();
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('sfp_by_id')->references('id')->on('users');
            $table->foreign('sfp_to_id')->references('id')->on('users');
        });

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('invoice_order_details');
        Schema::dropIfExists('invoice_order_columns');
        Schema::dropIfExists('invoice_statuses');
        Schema::dropIfExists('invoice_sfp');
    }

};
