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
        Schema::create('user_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->date('date');
            $table->integer('task_tested')->nullable();
            $table->integer('bug_reported')->nullable();
            $table->integer('regression')->nullable();
            $table->integer('smoke_testing')->nullable();
            $table->integer('client_meeting')->nullable();
            $table->integer('daily_meeting')->nullable();
            $table->integer('mobile_testing')->nullable();
            $table->string('description')->nullable();
            $table->string('other')->nullable()->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_reports');
    }
};
