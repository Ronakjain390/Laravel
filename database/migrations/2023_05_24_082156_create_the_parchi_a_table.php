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
        Schema::create('section', function (Blueprint $table) {
            $table->increments('id');
            $table->string('section', 255)->nullable();
            $table->enum('status', ['active', 'pause', 'terminated'])->nullable();
            $table->timestamps();
        });

        Schema::create('panel', function (Blueprint $table) {
            $table->increments('id');
            $table->string('panel_name', 255)->nullable();
            $table->unsignedInteger('section_id')->nullable();
            $table->enum('status', ['active', 'pause', 'terminated'])->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('section_id')->references('id')->on('section');
        });

        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('special_id', 10)->nullable();
            $table->string('name', 191)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('password', 191)->nullable();
            $table->string('device_token', 255)->nullable();
            $table->text('address')->nullable();
            $table->integer('pincode')->nullable();
            $table->string('company_name', 191)->nullable();
            $table->string('phone', 191)->nullable();
            $table->string('gst_number', 191)->nullable();
            $table->string('pancard', 191)->nullable();
            $table->string('state', 75)->nullable();
            $table->string('city', 75)->nullable();
            $table->string('bank_name', 255)->nullable();
            $table->string('branch_name', 255)->nullable();
            $table->string('bank_account_no', 255)->nullable();
            $table->string('ifsc_code', 255)->nullable();
            $table->string('tan', 15)->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->enum('status', ['active', 'pause', 'terminate'])->nullable();
            $table->boolean('sender')->nullable();
            $table->boolean('receiver')->nullable();
            $table->boolean('seller')->nullable();
            $table->boolean('buyer')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('is_phone_verified')->nullable();
            $table->tinyInteger('first_time')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->boolean('test_user')->default(0);
            $table->biginteger('added_by')->nullable();
            $table->timestamps();

            // Unique constraints
            $table->unique('phone');
            $table->unique('email');
            $table->unique('special_id');
        });

        Schema::create('teams', function (Blueprint $table) {
            $table->increments('id');
            $table->string('team_name', 255)->nullable();
            $table->string('team_owner_user', 255)->nullable();
            $table->unsignedBigInteger('team_owner_user_id')->nullable();
            $table->string('team_name_slug', 255)->nullable();
            $table->enum('status', ['active', 'pause', 'terminated'])->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('team_owner_user_id')->references('id')->on('users');
        });

        Schema::create('feature_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('feature_type_name', 255)->nullable();
            $table->enum('status', ['active', 'pause', 'terminated'])->nullable();
            $table->timestamps();
        });

        Schema::create('features', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('feature_type_id')->nullable();
            $table->unsignedInteger('panel_id')->nullable();
            $table->unsignedInteger('section_id')->nullable();
            $table->string('feature_name', 255)->nullable();
            $table->unsignedInteger('template_id')->nullable();
            $table->enum('status', ['active', 'pause', 'terminated'])->nullable();
            $table->timestamps();


            // Foreign key constraints
            $table->foreign('feature_type_id')->references('id')->on('feature_type');
            $table->foreign('panel_id')->references('id')->on('panel');
            $table->foreign('section_id')->references('id')->on('section');
            $table->foreign('template_id')->references('id')->on('templates');
        });

        // Schema::create('panel_features', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->string('panel_feature_name', 255)->nullable();
        //     $table->unsignedInteger('panel_id')->nullable();
        //     $table->unsignedInteger('section_id')->nullable();
        //     $table->unsignedInteger('feature_id')->nullable();
        //     $table->enum('status', ['active', 'pause', 'terminated'])->nullable();
        //     $table->timestamps();

        //     // Foreign key constraints
        //     $table->foreign('panel_id')->references('id')->on('panel');
        //     $table->foreign('section_id')->references('id')->on('section');
        //     $table->foreign('feature_id')->references('id')->on('features');
        // });

        Schema::create('templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('template_name', 255)->nullable();
            $table->string('template_page_name', 255)->nullable();
            $table->enum('status', ['active', 'pause', 'terminated'])->nullable();
            $table->string('template_image')->nullable(); // Add the template image column
            $table->timestamps();
        });
        Schema::create('additional_features', function (Blueprint $table) {
            $table->increments('id');
            $table->string('additional_feature_name');
            $table->unsignedInteger('section_id');
            $table->unsignedInteger('panel_id');
            $table->unsignedInteger('feature_id');
            $table->enum('status', ['active', 'pause', 'terminated']);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));

            $table->foreign('section_id')->references('id')->on('section');
            $table->foreign('panel_id')->references('id')->on('panel');
            $table->foreign('feature_id')->references('id')->on('features');
        });

        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191);
            $table->string('email', 191)->unique();
            $table->string('password', 191);
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('dashboard', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dashboard_name');
            $table->unsignedInteger('panel_id')->nullable();
            $table->unsignedInteger('section_id')->nullable();
            $table->enum('status', ['active', 'pause', 'terminated']);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));

            $table->foreign('panel_id')->references('id')->on('panel');
            $table->foreign('section_id')->references('id')->on('section');
        });

        Schema::create('panel_columns', function (Blueprint $table) {
            $table->increments('id');
            $table->string('panel_column_default_name');
            $table->string('panel_column_display_name');
            $table->enum('default', ['0', '1'])->default('0');
            // $table->enum('fixed', ['0', '1'])->default('1');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('feature_id');
            $table->unsignedInteger('panel_id');
            $table->unsignedInteger('section_id');
            // $table->unsignedInteger('feature_id');
            $table->enum('status', ['active', 'pause', 'terminated']);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('panel_id')->references('id')->on('panel');
            $table->foreign('feature_id')->references('id')->on('features');
            $table->foreign('section_id')->references('id')->on('section');
        });

        Schema::create('pdf_template', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pdf_name');
            $table->string('pdf_template_name');
            $table->enum('status', ['active', 'pause', 'terminated']);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });

        // Schema::create('panel_design', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->string('feature_name');
        //     $table->unsignedInteger('feature_id')->nullable();
        //     $table->unsignedInteger('panel_column_id')->nullable();
        //     $table->unsignedInteger('template_id');
        //     $table->unsignedInteger('pdf_template_id')->nullable();
        //     $table->enum('status', ['active', 'pause', 'terminated']);
        //     $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        //     $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));

        //     $table->foreign('feature_id')->references('id')->on('features')->onDelete('set null')->onUpdate('set null');
        //     $table->foreign('panel_column_id')->references('id')->on('panel_columns');
        //     $table->foreign('template_id')->references('id')->on('templates');
        //     $table->foreign('pdf_template_id')->references('id')->on('pdf_template');
        // });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section');
        Schema::dropIfExists('panel');
        Schema::dropIfExists('users');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('feature_type');
        // Schema::dropIfExists('panel_features');
        Schema::dropIfExists('features');
        Schema::dropIfExists('templates');
        Schema::dropIfExists('additional_features');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('dashboard');
        Schema::dropIfExists('panel_columns');
        // Schema::dropIfExists('panel_design');
        Schema::dropIfExists('pdf_template');
    }
};
