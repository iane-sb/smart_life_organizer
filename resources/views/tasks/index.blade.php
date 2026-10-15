@extends('layouts.app')

@section('title', 'Tasks')
@section('page-title', 'Task Manager')

@section('header-actions')
    <div class="tab-group">
        <a href="?view=kanban{{ request()->has('priority') ? '&priority='.request('priority') : '' }}" class="tab-btn {{ $view === 'kanban' ? 'active' : '' }}">
            <i data-lucide="layout-grid" style="width:13px;height:13px;display:inline;"></i> Kanban
        </a>
        <a href="?view=list{{ request()->has('priority') ? '&priority='.request('priority') : '' }}" class="tab-btn {{ $view === 'list' ? 'active' : '' }}">
            <i data-lucide="list" style="width:13px;height:13px;display:inline;"></i> List
        </a>
    </div>
    <button class="btn btn-primary" onclick="document.getElementById('create-task-modal').style.display='flex'">
        <i data-lucide="plus" style="width:14px;height:14px;"></i>
        New Task
    </button>
@endsection

@section('content')
<div x-data="{ createOpen: false, editTask: null }">

    <!-- Filter Bar -->
    <form method="GET" class="filter-bar" style="margin-bottom:20px;">
        <input type="hidden" name="view" value="{{ $view }}">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Search tasks…" class="form-control" style="width:220px;">
        <select name="priority" class="form-control" style="width:140px;" onchange="this.form.submit()">
            <option value="">All Priorities</option>
            @foreach(['urgent','high','medium','low'] as $p)
                <option value="{{ $p }}" {{ request('priority') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
            @endforeach
        </select>
        <select name="status" class="form-control" style="width:140px;" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
        </select>
        <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
        @if(request()->hasAny(['search','priority','status']))
            <a href="{{ route('tasks.index', ['view' => $view]) }}" class="btn btn-ghost btn-sm">Clear</a>
        @endif
    </form>

    @if($view === 'kanban')
    <!-- ===== KANBAN VIEW ===== -->
    <div class="kanban-board">

        <!-- Pending Column -->
        <div class="kanban-col">
            <div class="kanban-header">
                <span style="color:#94a3b8;">⏳ Pending</span>
                <span class="kanban-count">{{ $pending->count() }}</span>
            </div>
            <div class="kanban-items">
                @forelse($pending as $task)
                @include('tasks._kanban_card', ['task' => $task])
                @empty
                <div style="text-align:center;padding:30px 10px;color:var(--text-muted);font-size:12px;">No pending tasks</div>
                @endforelse
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="kanban-col">
            <div class="kanban-header">
                <span style="color:#60a5fa;">🚀 In Progress</span>
                <span class="kanban-count">{{ $inProgress->count() }}</span>
            </div>
            <div class="kanban-items">
                @forelse($inProgress as $task)
                @include('tasks._kanban_card', ['task' => $task])
                @empty
                <div style="text-align:center;padding:30px 10px;color:var(--text-muted);font-size:12px;">Nothing in progress</div>
                @endforelse
            </div>
        </div>

        <!-- Completed Column -->
        <div class="kanban-col">
            <div class="kanban-header">
                <span style="color:#34d399;">✅ Completed</span>
                <span class="kanban-count">{{ $completed->count() }}</span>
            </div>
            <div class="kanban-items">
                @forelse($completed as $task)
                @include('tasks._kanban_card', ['task' => $task])
                @empty
                <div style="text-align:center;padding:30px 10px;color:var(--text-muted);font-size:12px;">No completed tasks</div>
                @endforelse
            </div>
        </div>
    </div>

    @else
    <!-- ===== LIST VIEW ===== -->
    <div class="glass-card" style="overflow:hidden;">
        @forelse($tasks as $task)
        <div class="task-item" style="padding:16px 20px;">
            <form action="{{ route('tasks.toggle', $task) }}" method="POST" style="margin:0;">
                @csrf @method('PATCH')
                <button type="submit" class="task-checkbox {{ $task->status === 'completed' ? 'completed' : '' }}" style="border:2px solid {{ $task->status === 'completed' ? 'var(--accent-green)' : 'var(--border-hover)' }};background:{{ $task->status === 'completed' ? 'var(--accent-green)' : 'transparent' }};cursor:pointer;">
                    @if($task->status === 'completed')
                        <i data-lucide="check" style="width:10px;height:10px;color:white;"></i>
                    @endif
                </button>
            </form>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <span class="task-title {{ $task->status === 'completed' ? 'completed' : '' }}">{{ $task->title }}</span>
                    <span class="badge {{ $task->getPriorityColorClass() }}">{{ $task->priority }}</span>
                    <span class="badge status-{{ $task->status }}">{{ str_replace('_', ' ', $task->status) }}</span>
                    @if($task->isOverdue())
                        <span class="overdue-indicator"><i data-lucide="alert-triangle" style="width:12px;height:12px;"></i> Overdue</span>
                    @endif
                </div>
                @if($task->description)
                    <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">{{ Str::limit($task->description, 80) }}</div>
                @endif
                <div class="task-meta">
                    @if($task->due_date)
                        <i data-lucide="calendar" style="width:12px;height:12px;"></i>
                        {{ $task->due_date->format('M j, Y') }}
                        @if($task->due_time)at {{ date('g:ia', strtotime($task->due_time)) }}@endif
                    @endif
                </div>
            </div>
            <div style="display:flex;gap:6px;flex-shrink:0;">
                <button class="btn-icon" onclick="openEdit({{ $task->id }}, '{{ addslashes($task->title) }}', '{{ addslashes($task->description ?? '') }}', '{{ $task->priority }}', '{{ $task->status }}', '{{ $task->due_date?->format('Y-m-d') ?? '' }}', '{{ $task->due_time ?? '' }}')">
                    <i data-lucide="pencil" style="width:14px;height:14px;"></i>
                </button>
                <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete this task?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-icon" style="color:#f87171;border-color:rgba(239,68,68,0.3);">
                        <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i data-lucide="check-square" style="width:48px;height:48px;"></i>
            <p style="font-size:16px;font-weight:600;color:var(--text-secondary);margin-bottom:8px;">No tasks yet!</p>
            <p>Create your first task to get started.</p>
        </div>
        @endforelse
    </div>
    @endif

    <!-- ===== CREATE TASK MODAL ===== -->
    <div id="create-task-modal" style="display:none;" class="modal-overlay" onclick="if(event.target===this)this.style.display='none'">
        <div class="modal-box">
            <div class="modal-header">
                <span class="modal-title">✨ Create New Task</span>
                <button class="btn-icon" onclick="document.getElementById('create-task-modal').style.display='none'">
                    <i data-lucide="x" style="width:16px;height:16px;"></i>
                </button>
            </div>
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <input type="hidden" name="view" value="{{ $view }}">
                <div class="form-group">
                    <label class="form-label">Task Title *</label>
                    <input type="text" name="title" class="form-control" placeholder="What needs to be done?" required autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" placeholder="Add details or notes…"></textarea>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Priority *</label>
                        <select name="priority" class="form-control" required>
                            <option value="low">🟢 Low</option>
                            <option value="medium" selected>🔵 Medium</option>
                            <option value="high">🟠 High</option>
                            <option value="urgent">🔴 Urgent</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-control" required>
                            <option value="pending" selected>Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Due Time</label>
                        <input type="time" name="due_time" class="form-control">
                    </div>
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
                    <button type="button" class="btn btn-ghost" onclick="document.getElementById('create-task-modal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Task</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== EDIT TASK MODAL ===== -->
    <div id="edit-task-modal" style="display:none;" class="modal-overlay" onclick="if(event.target===this)this.style.display='none'">
        <div class="modal-box">
            <div class="modal-header">
                <span class="modal-title">✏️ Edit Task</span>
                <button class="btn-icon" onclick="document.getElementById('edit-task-modal').style.display='none'">
                    <i data-lucide="x" style="width:16px;height:16px;"></i>
                </button>
            </div>
            <form id="edit-task-form" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="view" value="{{ $view }}">
                <div class="form-group">
                    <label class="form-label">Task Title *</label>
                    <input type="text" id="edit-title" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea id="edit-description" name="description" class="form-control"></textarea>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Priority *</label>
                        <select id="edit-priority" name="priority" class="form-control" required>
                            <option value="low">🟢 Low</option>
                            <option value="medium">🔵 Medium</option>
                            <option value="high">🟠 High</option>
                            <option value="urgent">🔴 Urgent</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status *</label>
                        <select id="edit-status" name="status" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Due Date</label>
                        <input type="date" id="edit-due-date" name="due_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Due Time</label>
                        <input type="time" id="edit-due-time" name="due_time" class="form-control">
                    </div>
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
                    <button type="button" class="btn btn-ghost" onclick="document.getElementById('edit-task-modal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function openEdit(id, title, desc, priority, status, dueDate, dueTime) {
    document.getElementById('edit-task-form').action = '/tasks/' + id;
    document.getElementById('edit-title').value = title;
    document.getElementById('edit-description').value = desc;
    document.getElementById('edit-priority').value = priority;
    document.getElementById('edit-status').value = status;
    document.getElementById('edit-due-date').value = dueDate;
    document.getElementById('edit-due-time').value = dueTime;
    document.getElementById('edit-task-modal').style.display = 'flex';
}

// Auto-open create modal on validation error
@if($errors->any() && old('_token'))
    document.getElementById('create-task-modal').style.display = 'flex';
@endif

lucide.createIcons();
</script>
@endpush
