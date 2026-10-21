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
        Schema::create('fitness_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('caption');
            $table->string('image_path')->nullable();
            $table->string('workout_type')->default('general');
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->unsignedInteger('calories_burned')->nullable();
            $table->timestamps();
        });

        Schema::create('fitness_post_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fitness_post_id')->constrained('fitness_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['fitness_post_id', 'user_id']);
        });

        Schema::create('fitness_post_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fitness_post_id')->constrained('fitness_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fitness_post_comments');
        Schema::dropIfExists('fitness_post_likes');
        Schema::dropIfExists('fitness_posts');
    }
};
