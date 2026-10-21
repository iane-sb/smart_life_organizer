<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FitnessPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'caption',
        'image_path',
        'workout_type',
        'duration_minutes',
        'calories_burned',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'calories_burned'  => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(FitnessPostLike::class, 'fitness_post_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(FitnessPostComment::class, 'fitness_post_id');
    }

    public function saves(): HasMany
    {
        return $this->hasMany(FitnessPostSave::class, 'fitness_post_id');
    }

    public function hides(): HasMany
    {
        return $this->hasMany(FitnessPostHide::class, 'fitness_post_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(FitnessPostReport::class, 'fitness_post_id');
    }

    public function isLikedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($this->relationLoaded('likes')) {
            return $this->likes->contains('user_id', $user->id);
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function isSavedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($this->relationLoaded('saves')) {
            return $this->saves->contains('user_id', $user->id);
        }

        return $this->saves()->where('user_id', $user->id)->exists();
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

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return asset('storage/' . $this->image_path);
    }

    public function getWorkoutBadgeColorAttribute(): string
    {
        return match (strtolower($this->workout_type ?? 'general')) {
            'running'        => '#f59e0b', // amber
            'weightlifting'  => '#7c3aed', // purple
            'cycling'        => '#06b6d4', // cyan
            'hiit'           => '#ef4444', // red
            'yoga'           => '#10b981', // emerald
            'walking'        => '#3b82f6', // blue
            'swimming'       => '#0891b2', // teal
            default          => '#8b5cf6', // purple accent
        };
    }
}
