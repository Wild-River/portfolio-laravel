<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (['posts', 'works'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->date('published_at')->nullable()->after('status');
            });

            // Existing rows are shown on the day they were created until edited.
            DB::table($table)->update(['published_at' => DB::raw('DATE(created_at)')]);

            Schema::table($table, function (Blueprint $table) {
                $table->date('published_at')->nullable(false)->index()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['posts', 'works'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropIndex(['published_at']);
                $table->dropColumn('published_at');
            });
        }
    }
};
