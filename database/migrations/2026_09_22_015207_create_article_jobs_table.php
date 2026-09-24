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
        Schema::create('article_jobs', function (Blueprint $table) {
            // $table->id();
            $table->foreignId('article_id')->primary()->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('location')->nullable();
            $table->string('apply_url');
            $table->string('salary_range')->nullable();
            $table->string('job_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_jobs');
    }
};
