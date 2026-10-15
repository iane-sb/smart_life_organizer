@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Good ' . (date('H') < 12 ? 'Morning' : (date('H') < 17 ? 'Afternoon' : 'Evening')) . ', ' . explode(' ', Auth::user()->name)[0] . ' 👋')

@section('header-actions')
    <a href="{{ route('tasks.index') }}" class="btn btn-primary btn-sm">
        <i data-lucide="plus" style="width:14px;height:14px;"></i>
        New Task
    </a>
@endsection

@section('content')
<div style="display:flex;flex-direction:column;gap:24px;">

    <!-- Stats Row -->
    <div class="grid-4">
        <div class="glass-card stat-card animate-in">
            <div class="stat-icon" style="background:rgba(124,58,237,0.15);">
                <i data-lucide="check-square" style="color:#a78bfa;width:22px;height:22px;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $totalTasks }}</div>
                <div class="stat-label">Total Tasks</div>
            </div>
        </div>
        <div class="glass-card stat-card animate-in">
            <div class="stat-icon" style="background:rgba(16,185,129,0.15);">
                <i data-lucide="check-circle-2" style="color:#34d399;width:22px;height:22px;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $completedToday }}</div>
                <div class="stat-label">Completed Today</div>
            </div>
        </div>
        <div class="glass-card stat-card animate-in">
            <div class="stat-icon" style="background:rgba(245,158,11,0.15);">
                <i data-lucide="clock" style="color:#fbbf24;width:22px;height:22px;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $pendingTasks }}</div>
                <div class="stat-label">Pending Tasks</div>
            </div>
        </div>
        <div class="glass-card stat-card animate-in">
            <div class="stat-icon" style="background:rgba(6,182,212,0.15);">
                <i data-lucide="sticky-note" style="color:#22d3ee;width:22px;height:22px;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $totalNotes }}</div>
                <div class="stat-label">Notes Saved</div>
            </div>
        </div>
    </div>

    <!-- Main Row -->
    <div class="grid-2" style="gap:24px;">

        <!-- Today's Tasks -->
        <div class="glass-card animate-in" style="overflow:hidden;">
            <div style="padding:18px 20px; border-bottom:1px solid var(--border);">
                <div class="section-header" style="margin-bottom:0;">
                    <div class="section-title">📋 Today's Tasks</div>
                    <a href="{{ route('tasks.index') }}" class="btn btn-ghost btn-sm">View All</a>
                </div>
            </div>
            @forelse($todayTasks as $task)
            <div class="task-item">
                <form action="{{ route('tasks.toggle', $task) }}" method="POST" style="margin:0;">
                    @csrf @method('PATCH')
                    <button type="submit" class="task-checkbox {{ $task->status === 'completed' ? 'completed' : '' }}" style="border:none;background:{{ $task->status === 'completed' ? 'var(--accent-green)' : 'transparent' }};cursor:pointer;">
                        @if($task->status === 'completed')
                            <i data-lucide="check" style="width:10px;height:10px;color:white;"></i>
                        @endif
                    </button>
                </form>
                <div style="flex:1;min-width:0;">
                    <div class="task-title {{ $task->status === 'completed' ? 'completed' : '' }}">{{ $task->title }}</div>
                    <div class="task-meta">
                        <span class="badge {{ $task->getPriorityColorClass() }}">{{ $task->priority }}</span>
                        @if($task->due_date)
                            <span>{{ $task->due_date->format('M j') }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <i data-lucide="party-popper" style="width:36px;height:36px;"></i>
                <p>No pending tasks for today!</p>
            </div>
            @endforelse
        </div>

        <!-- Upcoming Events -->
        <div class="glass-card animate-in" style="overflow:hidden;">
            <div style="padding:18px 20px; border-bottom:1px solid var(--border);">
                <div class="section-header" style="margin-bottom:0;">
                    <div class="section-title">📅 Upcoming Events</div>
                    <a href="{{ route('calendar.index') }}" class="btn btn-ghost btn-sm">View Calendar</a>
                </div>
            </div>
            @forelse($upcomingEvents as $event)
            <div class="task-item">
                <div style="width:4px;height:40px;border-radius:4px;flex-shrink:0;background:{{ $event->color }};"></div>
                <div style="flex:1;min-width:0;">
                    <div class="task-title">{{ $event->title }}</div>
                    <div class="task-meta">
                        <span>{{ $event->start_datetime->format('M j, g:ia') }}</span>
                        <span class="badge status-pending">{{ ucfirst($event->category) }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <i data-lucide="calendar-check" style="width:36px;height:36px;"></i>
                <p>No upcoming events this week!</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Bottom Row: Chart + Streak -->
    <div class="grid-2" style="gap:24px;">

        <!-- Weekly Progress Chart -->
        <div class="glass-card animate-in" style="padding:24px;">
            <div class="section-header">
                <div class="section-title">📈 Weekly Progress</div>
                <div class="streak-badge">
                    🔥 {{ $streak }} day streak
                </div>
            </div>
            <canvas id="weeklyChart" height="180"></canvas>
        </div>

        <!-- Progress Ring -->
        <div class="glass-card animate-in" style="padding:24px;">
            <div class="section-title" style="margin-bottom:20px;">🎯 Today's Progress</div>
            <div style="display:flex;align-items:center;gap:30px;">
                <canvas id="progressRing" width="140" height="140"></canvas>
                <div>
                    <div style="font-size:36px;font-weight:800;color:var(--text-primary);">
                        {{ $totalTasks > 0 ? round(($completedToday / max($totalTasks, 1)) * 100) : 0 }}%
                    </div>
                    <div style="font-size:13px;color:var(--text-muted);margin-top:4px;">Tasks Completed</div>
                    <div style="margin-top:16px;display:flex;flex-direction:column;gap:8px;">
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text-secondary);">
                            <span style="width:10px;height:10px;border-radius:50%;background:#7c3aed;display:inline-block;"></span>
                            Completed: {{ $completedToday }}
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text-secondary);">
                            <span style="width:10px;height:10px;border-radius:50%;background:rgba(255,255,255,0.1);display:inline-block;border:1px solid var(--border);"></span>
                            Remaining: {{ $pendingTasks }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Weekly Bar Chart
const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
new Chart(weeklyCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($weeklyLabels) !!},
        datasets: [{
            label: 'Tasks Completed',
            data: {!! json_encode($weeklyData) !!},
            backgroundColor: 'rgba(124,58,237,0.4)',
            borderColor: '#7c3aed',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#64748b' } },
            y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#64748b', stepSize: 1 }, beginAtZero: true }
        }
    }
});

// Progress Doughnut
const ringCtx = document.getElementById('progressRing').getContext('2d');
const completed = {{ $completedToday }};
const pending   = {{ $pendingTasks }};
new Chart(ringCtx, {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [completed, Math.max(pending, completed === 0 && pending === 0 ? 1 : 0)],
            backgroundColor: ['#7c3aed', 'rgba(255,255,255,0.08)'],
            borderWidth: 0,
            borderRadius: 4,
        }]
    },
    options: {
        cutout: '75%',
        plugins: { legend: { display: false }, tooltip: { enabled: false } },
    }
});

lucide.createIcons();
</script>
@endpush
