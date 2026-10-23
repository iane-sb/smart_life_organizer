<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FitnessPostComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'fitness_post_id',
        'parent_id',
        'user_id',
        'comment',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(FitnessPost::class, 'fitness_post_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(FitnessPostComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(FitnessPostComment::class, 'parent_id');
    }

    public function hides(): HasMany
    {
        return $this->hasMany(FitnessCommentHide::class, 'fitness_post_comment_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(FitnessCommentReport::class, 'fitness_post_comment_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(FitnessCommentReaction::class, 'fitness_post_comment_id');
    }

    public function isReply(): bool
    {
        return ! is_null($this->parent_id);
    }

    public function isHiddenBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($this->relationLoaded('hides')) {
            return $this->hides->contains('user_id', $user->id);
        }

        return $this->hides()->where('user_id', $user->id)->exists();
    }

    public function isReportedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($this->relationLoaded('reports')) {
            return $this->reports->contains('user_id', $user->id);
        }

        return $this->reports()->where('user_id', $user->id)->exists();
    }

    public function isEdited(): bool
    {
        return $this->updated_at && $this->created_at && $this->updated_at->gt($this->created_at);
    }

    public function userReaction(?User $user): ?string
    {
        if (! $user) {
            return null;
        }

        if ($this->relationLoaded('reactions')) {
            return $this->reactions->firstWhere('user_id', $user->id)?->reaction_type;
        }

        return $this->reactions()->where('user_id', $user->id)->value('reaction_type');
    }

    public function reactionCounts(): array
    {
        if ($this->relationLoaded('reactions')) {
            return $this->reactions->groupBy('reaction_type')->map->count()->toArray();
        }

        return $this->reactions()
            ->selectRaw('reaction_type, count(*) as count')
            ->groupBy('reaction_type')
            ->pluck('count', 'reaction_type')
            ->toArray();
    }

    public function totalReactions(): int
    {
        if ($this->relationLoaded('reactions')) {
            return $this->reactions->count();
        }

        return $this->reactions()->count();
    }
}
