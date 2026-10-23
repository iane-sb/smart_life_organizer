<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FitnessCommentReaction extends Model
{
    use HasFactory;

    public const ALLOWED_TYPES = ['like', 'love', 'celebrate', 'laugh', 'fire'];

    protected $fillable = [
        'fitness_post_comment_id',
        'user_id',
        'reaction_type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(FitnessPostComment::class, 'fitness_post_comment_id');
    }

    public static function emoji(string $type): string
    {
        return match ($type) {
            'like'      => '👍',
            'love'      => '❤️',
            'celebrate' => '🎉',
            'laugh'     => '😂',
            'fire'      => '🔥',
            default     => '👍',
        };
    }
}
