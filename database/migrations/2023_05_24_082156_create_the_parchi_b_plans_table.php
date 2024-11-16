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

        Schema::create('plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('plan_name', 255);
            $table->decimal('price', 10, 2)->nullable();
            $table->unsignedInteger('section_id')->nullable();
            $table->unsignedInteger('panel_id')->nullable();
            $table->tinyInteger('is_enterprise_plan')->nullable()->default(0); // New column for enterprise plan check
            $table->unsignedBigInteger('enterprise_user_id')->nullable(); // New column for associating the plan with a specific user
            $table->integer('validity_days')->nullable(); // New column for plan validity in days
            $table->enum('status', ['active', 'pause', 'terminated']);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('section_id')->references('id')->on('section');
            $table->foreign('panel_id')->references('id')->on('panel');
            $table->foreign('enterprise_user_id')->references('id')->on('users'); // Assuming 'users' is the table name for enterprise users
        });

        Schema::create('plan_features', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('plan_id');
            $table->unsignedInteger('feature_id')->nullable();
            $table->integer('feature_usage_limit')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('plan_id')->references('id')->on('plans');
            $table->foreign('feature_id')->references('id')->on('features');
        });

        Schema::create('plan_additional_features', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('plan_id');
            $table->unsignedInteger('additional_feature_id')->nullable();
            $table->integer('additional_feature_usage_limit')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('plan_id')->references('id')->on('plans');
            $table->foreign('additional_feature_id')->references('id')->on('additional_features');
        });

        Schema::create('feature_topups', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('feature_id');
            $table->decimal('price', 8, 2);
            $table->integer('usage_limit')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('feature_id')->references('id')->on('features');
        });

        Schema::create('additional_feature_topups', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('additional_feature_id');
            $table->decimal('price', 8, 2);
            $table->integer('usage_limit')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('additional_feature_id')->references('id')->on('additional_features');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
        Schema::dropIfExists('plan_features');
        Schema::dropIfExists('plan_additional_features');
        Schema::dropIfExists('feature_topups');
        Schema::dropIfExists('additional_feature_topups');
    }
};
