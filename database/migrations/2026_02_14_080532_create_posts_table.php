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
        Schema::create('posts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('users')->onDelete('cascade');
            $table->string('post_title');
            $table->string('location');
            $table->longText('overview');
            $table->longText('responsibilities');
            $table->longText('requirements');
            $table->longText('skills');
            $table->unsignedInteger('experience_year');
            $table->enum('employment_type', ['work_from_home', 'full_time', 'remote', 'contract'])->comment('work_from_home, full_time, remote, contract');
            $table->enum('level_type', ['junior', 'middle', 'senior', 'head'])->comment('junior, middle, senior, head');
            $table->string('salary');
            $table->integer('total_applied')->default(0);
            $table->timestamps();

            // $table->foreign('company_id')->on('users')->references('id')->onDelete('cascade');

            $table->index('company_id');
            $table->index('post_title');
        });


        Schema::create('post_applies', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('post_id')->constrained('posts')->onDelete('cascade');
            $table->foreignUlid('user_id')->constrained('users')->onDelete('cascade');

            $table->enum('status', ['PENDING', 'ON_REVIEW', 'ACCEPTED', 'REJECTED'])->default('PENDING');

            $table->timestamps();

            // unique: 1 user hanya boleh apply 1x ke 1 post
            $table->unique(['user_id', 'post_id']);

            // $table->foreign('user_id')->on('users')->references('id')->onDelete('cascade');

            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('post_applies');
    }
};
