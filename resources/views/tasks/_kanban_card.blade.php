<div class="kanban-card">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:10px;">
        <div style="font-size:13.5px;font-weight:600;color:{{ $task->status === 'completed' ? 'var(--text-muted)' : 'var(--text-primary)' }};text-decoration:{{ $task->status === 'completed' ? 'line-through' : 'none' }};">
            {{ $task->title }}
        </div>
        <div style="display:flex;gap:4px;flex-shrink:0;">
            <button class="btn-icon" style="padding:4px;" onclick="openEdit({{ $task->id }}, '{{ addslashes($task->title) }}', '{{ addslashes($task->description ?? '') }}', '{{ $task->priority }}', '{{ $task->status }}', '{{ $task->due_date?->format('Y-m-d') ?? '' }}', '{{ $task->due_time ?? '' }}')">
                <i data-lucide="pencil" style="width:12px;height:12px;"></i>
            </button>
            <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete?')" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" class="btn-icon" style="padding:4px;color:#f87171;border-color:rgba(239,68,68,0.3);">
                    <i data-lucide="trash-2" style="width:12px;height:12px;"></i>
                </button>
            </form>
        </div>
    </div>

    @if($task->description)
    <div style="font-size:12px;color:var(--text-muted);margin-bottom:10px;line-height:1.5;">{{ Str::limit($task->description, 60) }}</div>
    @endif

    <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;flex-wrap:wrap;">
        <span class="badge {{ $task->getPriorityColorClass() }}">{{ $task->priority }}</span>
        <div style="display:flex;align-items:center;gap:6px;">
            @if($task->isOverdue())
                <span class="overdue-indicator" title="Overdue!">
                    <i data-lucide="alert-triangle" style="width:11px;height:11px;"></i>
                </span>
            @endif
            @if($task->due_date)
                <span style="font-size:11px;color:var(--text-muted);">
                    <i data-lucide="calendar" style="width:11px;height:11px;display:inline;"></i>
                    {{ $task->due_date->format('M j') }}
                </span>
            @endif
        </div>
    </div>

    <div style="margin-top:10px;">
        <form action="{{ route('tasks.toggle', $task) }}" method="POST" style="margin:0;">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;font-size:11px;">
                @if($task->status === 'completed')
                    <i data-lucide="rotate-ccw" style="width:12px;height:12px;"></i> Mark Pending
                @else
                    <i data-lucide="check" style="width:12px;height:12px;"></i> Complete
                @endif
            </button>
        </form>
    </div>
</div>
