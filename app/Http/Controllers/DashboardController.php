<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Note;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $today  = Carbon::today();

        $totalTasks     = Task::forUser($userId)->count();
        $completedToday = Task::forUser($userId)
            ->where('status', 'completed')
            ->whereDate('completed_at', $today)
            ->count();
        $pendingTasks   = Task::forUser($userId)->where('status', 'pending')->count();
        $totalNotes     = Note::forUser($userId)->count();

        $todayTasks = Task::forUser($userId)
            ->where('status', '!=', 'completed')
            ->where(function ($q) use ($today) {
                $q->whereDate('due_date', $today)
                  ->orWhereNull('due_date');
            })
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->take(6)
            ->get();

        $upcomingEvents = Event::forUser($userId)
            ->where('start_datetime', '>=', now())
            ->where('start_datetime', '<=', now()->addDays(7))
            ->orderBy('start_datetime')
            ->take(5)
            ->get();

        // Weekly chart data (last 7 days completed)
        $weeklyData = [];
        $weeklyLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $weeklyLabels[] = $date->format('D');
            $weeklyData[]   = Task::forUser($userId)
                ->where('status', 'completed')
                ->whereDate('completed_at', $date)
                ->count();
        }

        // Streak calculation
        $streak = 0;
        $checkDate = Carbon::today();
        while (true) {
            $count = Task::forUser($userId)
                ->where('status', 'completed')
                ->whereDate('completed_at', $checkDate)
                ->count();
            if ($count === 0) break;
            $streak++;
            $checkDate->subDay();
        }

        return view('dashboard.index', compact(
            'totalTasks', 'completedToday', 'pendingTasks', 'totalNotes',
            'todayTasks', 'upcomingEvents', 'weeklyData', 'weeklyLabels', 'streak'
        ));
    }
}
