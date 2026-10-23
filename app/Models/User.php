<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'username',
        'phone',
        'birthday',
        'bio',
        'theme_preference',
        'language',
        'date_format',
        'time_format',
        'notify_email',
        'notify_tasks',
        'notify_events',
        'default_dashboard_view',
        'two_factor_enabled',
        'privacy_visibility',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'   => 'datetime',
            'password'            => 'hashed',
            'birthday'            => 'date',
            'notify_email'        => 'boolean',
            'notify_tasks'        => 'boolean',
            'notify_events'       => 'boolean',
            'two_factor_enabled'  => 'boolean',
        ];
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function fitnessPosts(): HasMany
    {
        return $this->hasMany(FitnessPost::class);
    }

    public function fitnessLikes(): HasMany
    {
        return $this->hasMany(FitnessPostLike::class);
    }

    public function fitnessComments(): HasMany
    {
        return $this->hasMany(FitnessPostComment::class);
    }

    public function fitnessSaves(): HasMany
    {
        return $this->hasMany(FitnessPostSave::class);
    }

    public function fitnessHides(): HasMany
    {
        return $this->hasMany(FitnessPostHide::class);
    }

    public function fitnessReports(): HasMany
    {
        return $this->hasMany(FitnessPostReport::class);
    }

    public function fitnessCommentHides(): HasMany
    {
        return $this->hasMany(FitnessCommentHide::class);
    }

    public function fitnessCommentReports(): HasMany
    {
        return $this->hasMany(FitnessCommentReport::class);
    }

    public function fitnessCommentReactions(): HasMany
    {
        return $this->hasMany(FitnessCommentReaction::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
                return $this->avatar;
            }
            return asset('storage/' . $this->avatar);
        }

        // Beautiful SVG avatar fallback via UI-Avatars with purple gradient theme
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=7c3aed&color=ffffff&bold=true&rounded=true&size=128';
    }

    public function getUsernameDisplayAttribute(): string
    {
        if (!empty($this->username)) {
            return ltrim($this->username, '@');
        }

        return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $this->name)) ?: 'user' . $this->id;
    }

    public function getMemberSinceAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('F Y') : 'Recent';
    }
}
