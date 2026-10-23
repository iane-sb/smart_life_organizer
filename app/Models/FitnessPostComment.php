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

    public function hides(): HasMany
    {
        return $this->hasMany(FitnessCommentHide::class, 'fitness_post_comment_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(FitnessCommentReport::class, 'fitness_post_comment_id');
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
}
