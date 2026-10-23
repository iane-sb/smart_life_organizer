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
        Schema::table('fitness_post_comments', function (Blueprint $table) {
            $table->foreignId('parent_id')
                ->nullable()
                ->after('fitness_post_id')
                ->constrained('fitness_post_comments')
                ->cascadeOnDelete();
        });

        Schema::create('fitness_comment_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fitness_post_comment_id')->constrained('fitness_post_comments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('reaction_type');
            $table->timestamps();

            $table->unique(['fitness_post_comment_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fitness_comment_reactions');

        Schema::table('fitness_post_comments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
        });
    }
};
