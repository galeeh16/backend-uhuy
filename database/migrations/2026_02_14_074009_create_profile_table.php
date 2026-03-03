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
        Schema::create('user_profile', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('location')->nullable();
            $table->string('full_address')->nullable();
            $table->string('about_me')->nullable();
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();
            $table->string('cv')->nullable();
            $table->string('portfolio')->nullable();
            $table->date('birth_date')->nullable();
            $table->unsignedInteger('experience_year')->nullable();
            $table->boolean('availability_for_work')->default(true);
            $table->timestamps();

            $table->index('user_id');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('company_profile', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('address')->nullable();
            $table->string('location')->nullable();
            $table->text('about_company')->nullable();
            $table->unsignedInteger('company_size')->nullable();
            $table->date('founded_in')->nullable();
            $table->string('photo')->nullable();
            $table->string('website_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('linked_in_url')->nullable();
            $table->timestamps();

            $table->index('company_id');
            // $table->foreign('company_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profile');
        Schema::dropIfExists('company_profile');
    }
};
