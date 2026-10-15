@extends('layouts.app')

@section('title', 'Calendar')
@section('page-title', 'Smart Calendar')

@section('header-actions')
    <div class="tab-group">
        <a href="?view=month&month={{ $monthDate->format('Y-m') }}" class="tab-btn {{ $view === 'month' ? 'active' : '' }}">Month</a>
        <a href="?view=week&week_start={{ $weekStart->format('Y-m-d') }}" class="tab-btn {{ $view === 'week' ? 'active' : '' }}">Week</a>
        <a href="?view=day&day={{ $dayDate->format('Y-m-d') }}" class="tab-btn {{ $view === 'day' ? 'active' : '' }}">Day</a>
    </div>
    <button class="btn btn-primary" onclick="document.getElementById('create-event-modal').style.display='flex'">
        <i data-lucide="plus" style="width:14px;height:14px;"></i>
        New Event
    </button>
@endsection

@section('content')

@if($view === 'month')
<!-- ===== MONTH VIEW ===== -->
<div class="glass-card" style="overflow:hidden;">
    <!-- Month Navigation -->
    <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid var(--border);">
        <a href="?view=month&month={{ $monthDate->copy()->subMonth()->format('Y-m') }}" class="btn btn-ghost btn-sm">
            <i data-lucide="chevron-left" style="width:16px;height:16px;"></i>
        </a>
        <div style="font-size:18px;font-weight:800;color:var(--text-primary);">
            {{ $monthDate->format('F Y') }}
        </div>
        <div style="display:flex;gap:8px;">
            <a href="?view=month&month={{ now()->format('Y-m') }}" class="btn btn-ghost btn-sm">Today</a>
            <a href="?view=month&month={{ $monthDate->copy()->addMonth()->format('Y-m') }}" class="btn btn-ghost btn-sm">
                <i data-lucide="chevron-right" style="width:16px;height:16px;"></i>
            </a>
        </div>
    </div>

    <div style="padding:16px;">
        <!-- Day Headers -->
        <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:1px;margin-bottom:2px;">
            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
            <div class="cal-day-header">{{ $day }}</div>
            @endforeach
        </div>

        <!-- Calendar Grid -->
        <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;">
            @php
                $current = $monthStart->copy();
                $today = now()->startOfDay();
            @endphp
            @while($current <= $monthEnd)
            @php
                $dateKey = $current->format('Y-m-d');
                $isToday = $current->isSameDay($today);
                $isCurrentMonth = $current->month === $monthDate->month;
            @endphp
            <div class="cal-day {{ !$isCurrentMonth ? 'other-month' : '' }} {{ $isToday ? 'today' : '' }}"
                 onclick="openCreateForDate('{{ $dateKey }}')">
                <div class="cal-day-num">{{ $current->day }}</div>
                @if(isset($monthEvents[$dateKey]))
                    @foreach($monthEvents[$dateKey]->take(3) as $event)
                    <span class="cal-event-dot" style="background:{{ $event->color }};"
                          onclick="event.stopPropagation(); openEditEvent({{ $event->id }}, '{{ addslashes($event->title) }}', '{{ addslashes($event->description ?? '') }}', '{{ $event->category }}', '{{ $event->color }}', '{{ $event->start_datetime->format('Y-m-d\TH:i') }}', '{{ $event->end_datetime?->format('Y-m-d\TH:i') ?? '' }}')">
                        {{ $event->title }}
                    </span>
                    @endforeach
                    @if($monthEvents[$dateKey]->count() > 3)
                        <span style="font-size:10px;color:var(--text-muted);padding:1px 4px;">+{{ $monthEvents[$dateKey]->count() - 3 }} more</span>
                    @endif
                @endif
            </div>
            @php $current->addDay() @endphp
            @endwhile
        </div>
    </div>
</div>

@elseif($view === 'week')
<!-- ===== WEEK VIEW ===== -->
<div class="glass-card" style="overflow:hidden;">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid var(--border);">
        <a href="?view=week&week_start={{ $weekStart->copy()->subWeek()->format('Y-m-d') }}" class="btn btn-ghost btn-sm">
            <i data-lucide="chevron-left" style="width:16px;height:16px;"></i>
        </a>
        <div style="font-size:16px;font-weight:700;color:var(--text-primary);">
            {{ $weekStart->format('M j') }} – {{ $weekEnd->format('M j, Y') }}
        </div>
        <div style="display:flex;gap:8px;">
            <a href="?view=week&week_start={{ now()->startOfWeek(\Carbon\Carbon::SUNDAY)->format('Y-m-d') }}" class="btn btn-ghost btn-sm">This Week</a>
            <a href="?view=week&week_start={{ $weekStart->copy()->addWeek()->format('Y-m-d') }}" class="btn btn-ghost btn-sm">
                <i data-lucide="chevron-right" style="width:16px;height:16px;"></i>
            </a>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:0;border-top:1px solid var(--border);">
        @php $day = $weekStart->copy(); $today = now()->startOfDay(); @endphp
        @for($i = 0; $i < 7; $i++)
        @php $dateKey = $day->format('Y-m-d'); $isToday = $day->isSameDay($today); @endphp
        <div style="border-right:{{ $i < 6 ? '1px solid var(--border)' : 'none' }};min-height:300px;">
            <div style="padding:12px 10px;border-bottom:1px solid var(--border);text-align:center;background:{{ $isToday ? 'rgba(124,58,237,0.1)' : 'transparent' }};">
                <div style="font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">{{ $day->format('D') }}</div>
                <div style="font-size:20px;font-weight:800;color:{{ $isToday ? '#a78bfa' : 'var(--text-primary)' }};margin-top:2px;">{{ $day->format('j') }}</div>
            </div>
            <div style="padding:8px;display:flex;flex-direction:column;gap:4px;">
                @if(isset($weekEvents[$dateKey]))
                    @foreach($weekEvents[$dateKey] as $event)
                    <div style="background:{{ $event->color }}22;border-left:3px solid {{ $event->color }};border-radius:4px;padding:6px 8px;cursor:pointer;"
                         onclick="openEditEvent({{ $event->id }}, '{{ addslashes($event->title) }}', '{{ addslashes($event->description ?? '') }}', '{{ $event->category }}', '{{ $event->color }}', '{{ $event->start_datetime->format('Y-m-d\TH:i') }}', '{{ $event->end_datetime?->format('Y-m-d\TH:i') ?? '' }}')">
                        <div style="font-size:11.5px;font-weight:600;color:var(--text-primary);">{{ Str::limit($event->title, 18) }}</div>
                        <div style="font-size:10px;color:var(--text-muted);">{{ $event->start_datetime->format('g:ia') }}</div>
                    </div>
                    @endforeach
                @endif
                <button onclick="openCreateForDate('{{ $dateKey }}')" style="background:transparent;border:1px dashed var(--border);border-radius:4px;padding:4px;cursor:pointer;color:var(--text-muted);font-size:10px;font-family:'Inter',sans-serif;margin-top:2px;" title="Add event">+</button>
            </div>
        </div>
        @php $day->addDay() @endphp
        @endfor
    </div>
</div>

@else
<!-- ===== DAY VIEW ===== -->
<div class="glass-card" style="overflow:hidden;">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid var(--border);">
        <a href="?view=day&day={{ $dayDate->copy()->subDay()->format('Y-m-d') }}" class="btn btn-ghost btn-sm">
            <i data-lucide="chevron-left" style="width:16px;height:16px;"></i>
        </a>
        <div style="font-size:18px;font-weight:800;color:var(--text-primary);">
            {{ $dayDate->format('l, F j, Y') }}
            @if($dayDate->isToday()) <span class="badge status-in_progress" style="font-size:11px;margin-left:8px;">Today</span> @endif
        </div>
        <div style="display:flex;gap:8px;">
            <a href="?view=day&day={{ now()->format('Y-m-d') }}" class="btn btn-ghost btn-sm">Today</a>
            <a href="?view=day&day={{ $dayDate->copy()->addDay()->format('Y-m-d') }}" class="btn btn-ghost btn-sm">
                <i data-lucide="chevron-right" style="width:16px;height:16px;"></i>
            </a>
        </div>
    </div>
    <div style="padding:20px;">
        @forelse($dayEvents as $event)
        <div style="display:flex;gap:16px;margin-bottom:16px;padding:16px;background:rgba(255,255,255,0.03);border-radius:var(--radius-md);border:1px solid var(--border);cursor:pointer;"
             onclick="openEditEvent({{ $event->id }}, '{{ addslashes($event->title) }}', '{{ addslashes($event->description ?? '') }}', '{{ $event->category }}', '{{ $event->color }}', '{{ $event->start_datetime->format('Y-m-d\TH:i') }}', '{{ $event->end_datetime?->format('Y-m-d\TH:i') ?? '' }}')">
            <div style="width:4px;border-radius:4px;background:{{ $event->color }};flex-shrink:0;"></div>
            <div style="flex:1;">
                <div style="font-size:15px;font-weight:700;color:var(--text-primary);margin-bottom:4px;">{{ $event->title }}</div>
                <div style="font-size:12px;color:var(--text-muted);">
                    {{ $event->start_datetime->format('g:ia') }}
                    @if($event->end_datetime) – {{ $event->end_datetime->format('g:ia') }} @endif
                    · {{ ucfirst($event->category) }}
                </div>
                @if($event->description)
                    <div style="font-size:13px;color:var(--text-secondary);margin-top:8px;">{{ $event->description }}</div>
                @endif
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i data-lucide="calendar-x" style="width:48px;height:48px;"></i>
            <p style="font-size:15px;font-weight:600;color:var(--text-secondary);margin-bottom:8px;">No events today</p>
            <button class="btn btn-primary" onclick="openCreateForDate('{{ $dayDate->format('Y-m-d') }}')">
                <i data-lucide="plus" style="width:14px;height:14px;"></i> Add Event
            </button>
        </div>
        @endforelse
    </div>
</div>
@endif

<!-- ===== CREATE EVENT MODAL ===== -->
<div id="create-event-modal" style="display:none;" class="modal-overlay" onclick="if(event.target===this)this.style.display='none'">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">📅 New Event</span>
            <button class="btn-icon" onclick="document.getElementById('create-event-modal').style.display='none'">
                <i data-lucide="x" style="width:16px;height:16px;"></i>
            </button>
        </div>
        <form action="{{ route('events.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Event Title *</label>
                <input type="text" name="title" class="form-control" placeholder="What's the event?" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" placeholder="Details…"></textarea>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Category *</label>
                    <select name="category" class="form-control" required>
                        <option value="personal">Personal</option>
                        <option value="work">Work</option>
                        <option value="study">Study</option>
                        <option value="appointment">Appointment</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Color</label>
                    <input type="color" name="color" id="create-event-color" value="#7c3aed" class="form-control" style="height:42px;padding:4px 8px;cursor:pointer;">
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Start *</label>
                    <input type="datetime-local" id="create-start" name="start_datetime" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">End</label>
                    <input type="datetime-local" name="end_datetime" class="form-control">
                </div>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('create-event-modal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Event</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== EDIT EVENT MODAL ===== -->
<div id="edit-event-modal" style="display:none;" class="modal-overlay" onclick="if(event.target===this)this.style.display='none'">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">✏️ Edit Event</span>
            <button class="btn-icon" onclick="document.getElementById('edit-event-modal').style.display='none'">
                <i data-lucide="x" style="width:16px;height:16px;"></i>
            </button>
        </div>
        <form id="edit-event-form" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Event Title *</label>
                <input type="text" id="edit-event-title" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea id="edit-event-desc" name="description" class="form-control"></textarea>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Category *</label>
                    <select id="edit-event-category" name="category" class="form-control" required>
                        <option value="personal">Personal</option>
                        <option value="work">Work</option>
                        <option value="study">Study</option>
                        <option value="appointment">Appointment</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Color</label>
                    <input type="color" id="edit-event-color" name="color" class="form-control" style="height:42px;padding:4px 8px;cursor:pointer;">
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Start *</label>
                    <input type="datetime-local" id="edit-event-start" name="start_datetime" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">End</label>
                    <input type="datetime-local" id="edit-event-end" name="end_datetime" class="form-control">
                </div>
            </div>
            <div style="display:flex;gap:10px;justify-content:space-between;margin-top:8px;">
                <form id="delete-event-form" method="POST" onsubmit="return confirm('Delete this event?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i data-lucide="trash-2" style="width:13px;height:13px;"></i> Delete
                    </button>
                </form>
                <div style="display:flex;gap:10px;">
                    <button type="button" class="btn btn-ghost" onclick="document.getElementById('edit-event-modal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openCreateForDate(dateStr) {
    document.getElementById('create-start').value = dateStr + 'T09:00';
    document.getElementById('create-event-modal').style.display = 'flex';
}

function openEditEvent(id, title, desc, category, color, start, end) {
    document.getElementById('edit-event-form').action = '/events/' + id;
    document.getElementById('delete-event-form').action = '/events/' + id;
    document.getElementById('edit-event-title').value = title;
    document.getElementById('edit-event-desc').value = desc;
    document.getElementById('edit-event-category').value = category;
    document.getElementById('edit-event-color').value = color;
    document.getElementById('edit-event-start').value = start;
    document.getElementById('edit-event-end').value = end;
    document.getElementById('edit-event-modal').style.display = 'flex';
}

lucide.createIcons();
</script>
@endpush
