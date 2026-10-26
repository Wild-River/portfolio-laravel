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
        Schema::create('github_language_stats', function (Blueprint $table) {
            $table->id();
            $table->string('language', 50)->unique();
            $table->unsignedBigInteger('bytes');
            $table->timestamp('fetched_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('github_language_stats');
    }
};
