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
        Schema::create('article_guides', function (Blueprint $table) {
            // $table->id();
            $table->foreignId('article_id')->primary()->constrained()->cascadeOnDelete();
            $table->string('certificate_code')->nullable(); // mis. "STCW", "BST", "COC"
            $table->string('applicable_rank')->nullable();  // mis. "Deck Officer", "Engine Officer"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_guides');
    }
};
