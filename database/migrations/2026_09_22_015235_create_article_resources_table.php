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
        Schema::create('article_resources', function (Blueprint $table) {
            // $table->id();
            $table->foreignId('article_id')->primary()->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_type', 20)->nullable();
            $table->unsignedInteger('file_size')->nullable(); // dalam KB
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_resources');
    }
};
