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
        Schema::table('user_reports', function (Blueprint $table) {
            // Add indexes to frequently queried columns
            $table->index('user_id');
            $table->index('project_id');
            $table->index('date');
            $table->index(['user_id', 'date']);
            $table->index(['project_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_reports', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['user_id']);
            $table->dropIndex(['project_id']);
            $table->dropIndex(['date']);
            $table->dropIndex(['user_id', 'date']);
            $table->dropIndex(['project_id', 'date']);
        });
    }
};
