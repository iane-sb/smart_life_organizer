<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'category', 'color',
        'start_datetime', 'end_datetime', 'is_all_day',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime'   => 'datetime',
        'is_all_day'     => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getCategoryColorAttribute(): string
    {
        return match($this->category) {
            'appointment' => '#f59e0b',
            'study'       => '#3b82f6',
            'work'        => '#8b5cf6',
            'personal'    => '#06b6d4',
            'other'       => '#6b7280',
            default       => '#7c3aed',
        };
    }
}
