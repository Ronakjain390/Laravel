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
        if (!Schema::hasTable('goods_receipts')) {
            Schema::create('goods_receipts', function (Blueprint $table) {
                $table->id();
                $table->string('goods_series');
                $table->string('series_num');
                $table->unsignedBigInteger('sender_id');
                $table->string('sender');
                $table->unsignedBigInteger('receiver_goods_receipts_id');
                $table->string('receiver_goods_receipts');
                $table->string('pdf_url')->nullable();
                $table->text('comment')->nullable();
                $table->json('status_comment')->nullable();
                $table->boolean('calculate_tax')->default(true);
                $table->decimal('total', 20, 2)->default(0.00);
                $table->bigInteger('receiver_goods_receipts_detail_id')->nullable();
                $table->bigInteger('team_id')->nullable();
                $table->string('additional_phone_number')->nullable();
                $table->string('signature')->nullable();
                $table->date('goods_receipts_date');
                $table->softDeletes();
                $table->timestamps();

                // Foreign key constraints
                $table->foreign('sender_id')->references('id')->on('users');
            });
        }

        if (!Schema::hasTable('goods_receipt_order_details')) {
            Schema::create('goods_receipt_order_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('goods_receipt_id');
                $table->string('unit')->nullable();
                $table->decimal('rate', 20, 2)->default(0.00)->nullable();
                $table->integer('qty')->default(0)->nullable();
                $table->string('details')->nullable();
                $table->decimal('tax', 20, 2)->default(0.00)->nullable();
                $table->decimal('discount', 20, 2)->default(0.00)->nullable();
                $table->decimal('total_amount', 20, 2)->default(0.00)->nullable();
                $table->timestamps();

                // Foreign key constraint
                $table->foreign('goods_receipt_id')->references('id')->on('goods_receipts')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('goods_receipt_order_columns')) {
            Schema::create('goods_receipt_order_columns', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('goods_receipt_order_detail_id');
                $table->string('column_name')->nullable();
                $table->string('column_value')->nullable();
                $table->timestamps();

                // Define foreign key constraint with a shorter name
                $table->foreign('goods_receipt_order_detail_id', 'gr_order_detail_fk')->references('id')->on('goods_receipt_order_details')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('goods_receipt_statuses')) {
            Schema::create('goods_receipt_statuses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('goods_receipt_id');
                $table->unsignedBigInteger('user_id');
                $table->string('status');
                $table->text('comment')->nullable();
                $table->timestamps();

                // Define foreign key constraint
                $table->foreign('goods_receipt_id')->references('id')->on('goods_receipts')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('goods_receipts_sfp')) {
            Schema::create('goods_receipts_sfp', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('goods_receipts_id');
                $table->unsignedBigInteger('sfp_by_id');
                $table->string('sfp_by_name');
                $table->unsignedBigInteger('sfp_to_id')->nullable();
                $table->string('sfp_to_name')->nullable();
                $table->text('comment')->nullable();
                $table->enum('status', ['accept', 'reject', 'sent'])->nullable();
                $table->timestamps();

                // Define foreign key constraints
                $table->foreign('goods_receipts_id', 'gr_sfp_receipts_fk')->references('id')->on('goods_receipts')->onDelete('cascade');
                $table->foreign('sfp_by_id')->references('id')->on('users');
                $table->foreign('sfp_to_id')->references('id')->on('users');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipts_sfp');
        Schema::dropIfExists('goods_receipt_statuses');
        Schema::dropIfExists('goods_receipt_order_columns');
        Schema::dropIfExists('goods_receipt_order_details');
        Schema::dropIfExists('goods_receipts');
    }
};
