<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'priority', 'status',
        'due_date', 'due_time', 'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function isOverdue(): bool
    {
        return $this->due_date &&
               $this->due_date->isPast() &&
               $this->status !== 'completed';
    }

    public function getPriorityColorClass(): string
    {
        return match($this->priority) {
            'urgent' => 'priority-urgent',
            'high'   => 'priority-high',
            'medium' => 'priority-medium',
            'low'    => 'priority-low',
            default  => 'priority-medium',
        };
    }
}
