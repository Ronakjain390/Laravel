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
        Schema::create('estimates', function (Blueprint $table) {
            $table->id();
            $table->string('estimate_series');
            $table->string('series_num');
            $table->unsignedBigInteger('seller_id');
            $table->string('seller');
            $table->unsignedBigInteger('buyer_id');
            $table->string('buyer');
            $table->unsignedBigInteger('buyer_detail_id')->nullable();
            $table->string('pdf_url')->nullable();
            $table->text('comment')->nullable();
            $table->json('status_comment')->nullable();
            $table->boolean('calculate_tax')->default(true);
            $table->decimal('total', 20, 2);
            $table->string('round_off')->nullable();
            $table->string('team_id')->nullable();
            $table->string('purchase_order_series')->nullable();
            $table->date('estimate_date');
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('seller_id')->references('id')->on('users');
            $table->foreign('buyer_id')->references('id')->on('users');
            $table->index(['seller_id', 'buyer_id', 'buyer_detail_id']);
        });

        Schema::create('estimate_order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estimate_id');
            $table->string('unit')->nullable();
            $table->decimal('rate', 20, 2)->nullable();
            $table->decimal('qty' , 20, 2 )->nullable();
            $table->string('details')->nullable();
            $table->decimal('tax', 20, 2)->nullable();
            $table->decimal('discount', 20, 2)->nullable();
            $table->decimal('total_amount', 20, 2)->nullable();
            $table->decimal('igst', 20, 2)->nullable();
            $table->decimal('cgst', 20, 2)->nullable();
            $table->decimal('sgst', 20, 2)->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('estimate_id')->references('id')->on('estimates')->onDelete('cascade');
            $table->index('estimate_id');
        });

        Schema::create('estimate_order_columns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estimate_order_detail_id');
            $table->string('column_name')->nullable();
            $table->string('column_value')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('estimate_order_detail_id')->references('id')->on('estimate_order_details')->onDelete('cascade');
            $table->index('estimate_order_detail_id');
        });

        Schema::create('estimate_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estimate_id');
            $table->unsignedBigInteger('user_id');
            $table->string('user_name');
            $table->string('team_user_name');
            $table->enum('status', ['draft', 'modified', 'sent', 'resent', 'accept', 'reject', 'self_accept', 'partially_self_return', 'self_return', 'self_reject', 'deleted','created']);
            $table->text('comment')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('estimate_id')->references('id')->on('estimates')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
            $table->index('estimate_id');
            $table->index('user_id');
        });

        Schema::create('estimate_sfp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estimate_id');
            $table->unsignedBigInteger('sfp_by_id');
            $table->string('sfp_by_name');
            $table->unsignedBigInteger('sfp_to_id')->nullable();
            $table->string('sfp_to_name')->nullable();
            $table->text('comment')->nullable();
            $table->enum('status', ['accept', 'reject', 'sent'])->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('estimate_id')->references('id')->on('estimates')->onDelete('cascade');
            $table->foreign('sfp_by_id')->references('id')->on('users');
            $table->foreign('sfp_to_id')->references('id')->on('users');
            $table->index('estimate_id');
            $table->index('sfp_by_id');
        });

        Schema::create('estimate_tags', function(Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('estimate_id');
            $table->unsignedBigInteger('tags_table_id');
            $table->unsignedBigInteger('user_id');
            $table->string('table_id');
            $table->unsignedInteger('panel_id');
            $table->timestamps();

            $table->foreign('estimate_id')->references('id')->on('estimates')->onDelete('cascade');
            $table->foreign('tags_table_id')->references('id')->on('tags_table')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('panel_id')->references('id')->on('panel')->onDelete('cascade');
            $table->index('estimate_id');
            $table->index('tags_table_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimate_sfp');
        Schema::dropIfExists('estimate_statuses');
        Schema::dropIfExists('estimate_order_columns');
        Schema::dropIfExists('estimate_order_details');
        Schema::dropIfExists('estimate_tags');
        Schema::dropIfExists('estimates');
    }
};
