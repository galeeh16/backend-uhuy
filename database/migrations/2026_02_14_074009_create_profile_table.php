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
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->unique()->constrained('users')->onDelete('cascade');
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
        });

        Schema::create('company_profile', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->unique()->constrained('users')->onDelete('cascade');
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
        });

        Schema::create('user_educations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('degree', ['SMA', 'SMK', 'S1', 'S2', 'S3']); // cth: Sarjana (S1), SMA/SMK
            $table->string('institution_name'); // cth: Universitas Indonesia
            $table->string('field_of_study'); // cth: Teknik Informatika, IPA, IPS
            
            $table->date('start_at');
            $table->date('end_at')->nullable(); // Nullable jika masih bersekolah/kuliah
            
            $table->string('grade')->nullable(); // cth: IPK 3.80 atau Nilai Akhir
            $table->text('description')->nullable(); // Untuk mencatat organisasi, pencapaian, dll.
            
            $table->timestamps();

            $table->index('user_id');
        });

        Schema::create('user_work_experiences', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('company_name');
            $table->string('position'); // cth: Frontend Developer
            $table->date('start_at');
            $table->date('end_at')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profile');
        Schema::dropIfExists('company_profile');
        Schema::dropIfExists('user_educations');
        Schema::dropIfExists('user_work_experiences');
    }
};
