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
        Schema::table('works', function (Blueprint $table) {
            // Required in the admin form; nullable here so existing works can be filled in later.
            $table->string('demo_url')->nullable()->after('description');
            $table->string('github_url')->nullable()->after('demo_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropColumn(['demo_url', 'github_url']);
        });
    }
};
