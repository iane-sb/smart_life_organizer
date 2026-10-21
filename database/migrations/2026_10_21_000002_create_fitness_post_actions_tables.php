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
        Schema::create('fitness_post_saves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fitness_post_id')->constrained('fitness_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['fitness_post_id', 'user_id']);
        });

        Schema::create('fitness_post_hides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fitness_post_id')->constrained('fitness_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['fitness_post_id', 'user_id']);
        });

        Schema::create('fitness_post_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fitness_post_id')->constrained('fitness_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('reason');
            $table->text('details')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->unique(['fitness_post_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fitness_post_reports');
        Schema::dropIfExists('fitness_post_hides');
        Schema::dropIfExists('fitness_post_saves');
    }
};
