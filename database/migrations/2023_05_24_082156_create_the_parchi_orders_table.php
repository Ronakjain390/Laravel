<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {

        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('plan_id');
            $table->unsignedInteger('section_id');
            $table->unsignedInteger('panel_id');
            $table->dateTime('purchase_date');
            $table->dateTime('expiry_date');
            $table->string('amount');
            $table->string('added_by');
            $table->enum('status', ['active', 'expired']);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('plan_id')->references('id')->on('plans');
            $table->foreign('section_id')->references('id')->on('section');
            $table->foreign('panel_id')->references('id')->on('panel');
        });


        Schema::create('plan_feature_usage_records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('plan_feature_id');
            $table->unsignedInteger('feature_id'); // New field for referencing the feature
            $table->integer('usage_count')->default(0);
            $table->integer('usage_limit'); // New field for storing the usage limit
            $table->enum('status', ['active', 'expired']);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('plan_feature_id')->references('id')->on('plan_features');
            $table->foreign('feature_id')->references('id')->on('features');
        });

        Schema::create('plan_additional_feature_usage_records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('plan_additional_feature_id');
            $table->unsignedInteger('additional_feature_id'); // New field for referencing the additional feature
            $table->integer('usage_count')->default(0);
            $table->integer('usage_limit'); // New field for storing the usage limit
            $table->enum('status', ['active', 'expired']);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('order_id')->references('id')->on('orders');
             // Specify a custom identifier name for the foreign key constraint
            $table->foreign('plan_additional_feature_id', 'pafr_p_additional_feature_id_foreign')->references('id')->on('plan_additional_features');
            $table->foreign('additional_feature_id', 'pafr_additional_feature_id_foreign')->references('id')->on('additional_features');
        });

        Schema::create('feature_topup_usage_records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('feature_topup_id');
            $table->unsignedInteger('feature_id'); // New field for referencing the feature
            $table->integer('usage_count')->default(0);
            $table->integer('usage_limit')->nullable();
            $table->string('amount');
            $table->string('added_by');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('feature_topup_id')->references('id')->on('feature_topups');
            $table->foreign('feature_id')->references('id')->on('features');
        });

        Schema::create('additional_feature_topup_usage_records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('additional_feature_topup_id');
            $table->unsignedInteger('additional_feature_id'); // New field for referencing the additional feature
            $table->integer('usage_count')->default(0);
            $table->integer('usage_limit')->nullable();
            $table->string('amount');
            $table->string('added_by');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('additional_feature_topup_id','aftr_additional_feature_t_id_foreign')->references('id')->on('additional_feature_topups');
            $table->foreign('additional_feature_id','aftr_additional_feature_id_foreign')->references('id')->on('additional_features');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('plan_feature_usage_records');
        Schema::dropIfExists('plan_additional_feature_usage_records');
        Schema::dropIfExists('feature_topup_usage_records');
        Schema::dropIfExists('additional_feature_topup_usage_records');
    }
};
