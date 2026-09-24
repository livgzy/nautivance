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
        Schema::create('article_careers', function (Blueprint $table) {
            // $table->id();
            $table->foreignId('article_id')->primary()->constrained()->cascadeOnDelete();
            $table->enum('career_stage', ['cadet', 'mualim_iii', 'mualim_ii', 'mualim_i', 'captain'])->nullable();
            $table->string('topic')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_careers');
    }
};
