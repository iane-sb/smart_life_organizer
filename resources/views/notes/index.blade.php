@extends('layouts.app')

@section('title', 'Notes')
@section('page-title', 'Notes & Ideas')

@section('header-actions')
    <button class="btn btn-primary" onclick="document.getElementById('create-note-modal').style.display='flex'">
        <i data-lucide="plus" style="width:14px;height:14px;"></i>
        New Note
    </button>
@endsection

@section('content')

<!-- Search Bar -->
<form method="GET" style="margin-bottom:20px;">
    <div style="display:flex;gap:10px;align-items:center;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Search notes…" class="form-control" style="max-width:320px;">
        <button type="submit" class="btn btn-ghost btn-sm">Search</button>
        @if(request('search'))
            <a href="{{ route('notes.index') }}" class="btn btn-ghost btn-sm">Clear</a>
        @endif
    </div>
</form>

<!-- Notes Count -->
@if($notes->count() > 0)
<div style="font-size:12px;color:var(--text-muted);margin-bottom:16px;">
    {{ $notes->count() }} note{{ $notes->count() !== 1 ? 's' : '' }}
    @if($notes->where('is_pinned', true)->count() > 0)
        · {{ $notes->where('is_pinned', true)->count() }} pinned
    @endif
</div>
@endif

<!-- Notes Masonry Grid -->
@if($notes->count() > 0)
<div class="notes-grid">
    @foreach($notes as $note)
    <div class="note-card {{ $note->is_pinned ? 'pinned' : '' }}">
        <!-- Color accent bar -->
        <div class="note-accent-bar" style="background:{{ $note->color }};"></div>

        @if($note->is_pinned)
        <div style="position:absolute;top:12px;right:12px;">
            <i data-lucide="pin" style="width:14px;height:14px;color:{{ $note->color }};"></i>
        </div>
        @endif

        <div class="note-title">{{ $note->title }}</div>

        @if($note->content)
        <div class="note-content">{{ Str::limit($note->content, 200) }}</div>
        @endif

        <div class="note-meta">
            <span class="note-date">{{ $note->updated_at->diffForHumans() }}</span>
            <div class="note-actions">
                <!-- Pin toggle -->
                <form action="{{ route('notes.pin', $note) }}" method="POST" style="margin:0;">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-icon" style="padding:5px;" title="{{ $note->is_pinned ? 'Unpin' : 'Pin' }}">
                        <i data-lucide="{{ $note->is_pinned ? 'pin-off' : 'pin' }}" style="width:13px;height:13px;color:{{ $note->is_pinned ? '#a78bfa' : 'var(--text-muted)' }};"></i>
                    </button>
                </form>
                <!-- Edit -->
                <button class="btn-icon" style="padding:5px;"
                    onclick="openEditNote({{ $note->id }}, '{{ addslashes($note->title) }}', '{{ addslashes($note->content ?? '') }}', '{{ $note->color }}')">
                    <i data-lucide="pencil" style="width:13px;height:13px;"></i>
                </button>
                <!-- Delete -->
                <form action="{{ route('notes.destroy', $note) }}" method="POST" onsubmit="return confirm('Delete this note?')" style="margin:0;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-icon" style="padding:5px;color:#f87171;border-color:rgba(239,68,68,0.3);">
                        <i data-lucide="trash-2" style="width:13px;height:13px;"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="glass-card">
    <div class="empty-state">
        <i data-lucide="notebook" style="width:56px;height:56px;"></i>
        <p style="font-size:16px;font-weight:600;color:var(--text-secondary);margin-bottom:8px;">
            {{ request('search') ? 'No notes match your search' : 'No notes yet!' }}
        </p>
        <p style="margin-bottom:16px;">{{ request('search') ? 'Try a different keyword.' : 'Capture your ideas, plans, and reminders.' }}</p>
        @if(!request('search'))
        <button class="btn btn-primary" onclick="document.getElementById('create-note-modal').style.display='flex'">
            <i data-lucide="plus" style="width:14px;height:14px;"></i> Create First Note
        </button>
        @endif
    </div>
</div>
@endif

<!-- ===== CREATE NOTE MODAL ===== -->
<div id="create-note-modal" style="display:none;" class="modal-overlay" onclick="if(event.target===this)this.style.display='none'">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">📝 New Note</span>
            <button class="btn-icon" onclick="document.getElementById('create-note-modal').style.display='none'">
                <i data-lucide="x" style="width:16px;height:16px;"></i>
            </button>
        </div>
        <form action="{{ route('notes.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Title *</label>
                <input type="text" name="title" class="form-control" placeholder="Note title…" required>
            </div>
            <div class="form-group">
                <label class="form-label">Content</label>
                <textarea name="content" class="form-control" placeholder="Write your note here…" style="min-height:140px;"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Card Color</label>
                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                    @foreach(['#7c3aed','#2563eb','#06b6d4','#10b981','#f59e0b','#ec4899','#ef4444','#64748b'] as $clr)
                    <label style="cursor:pointer;">
                        <input type="radio" name="color" value="{{ $clr }}" style="display:none;" {{ $clr === '#7c3aed' ? 'checked' : '' }}>
                        <span style="display:block;width:28px;height:28px;border-radius:50%;background:{{ $clr }};border:3px solid transparent;transition:border-color 0.15s;"
                              onclick="this.previousElementSibling.checked=true;document.querySelectorAll('[name=color]').forEach(r=>r.parentElement.querySelector('span').style.borderColor='transparent');this.style.borderColor='white';"></span>
                    </label>
                    @endforeach
                    <input type="color" name="color_custom" placeholder="Custom" class="form-control" style="width:42px;height:36px;padding:2px;cursor:pointer;" title="Custom color" oninput="document.querySelectorAll('input[name=color]').forEach(r=>r.checked=false);document.querySelector('input[name=color][value=\\'#7c3aed\\']').value=this.value;">
                </div>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('create-note-modal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Note</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== EDIT NOTE MODAL ===== -->
<div id="edit-note-modal" style="display:none;" class="modal-overlay" onclick="if(event.target===this)this.style.display='none'">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">✏️ Edit Note</span>
            <button class="btn-icon" onclick="document.getElementById('edit-note-modal').style.display='none'">
                <i data-lucide="x" style="width:16px;height:16px;"></i>
            </button>
        </div>
        <form id="edit-note-form" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Title *</label>
                <input type="text" id="edit-note-title" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Content</label>
                <textarea id="edit-note-content" name="content" class="form-control" style="min-height:140px;"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Card Color</label>
                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                    @foreach(['#7c3aed','#2563eb','#06b6d4','#10b981','#f59e0b','#ec4899','#ef4444','#64748b'] as $clr)
                    <label style="cursor:pointer;" id="edit-color-label-{{ str_replace('#','',$clr) }}">
                        <input type="radio" name="color" value="{{ $clr }}" style="display:none;">
                        <span style="display:block;width:28px;height:28px;border-radius:50%;background:{{ $clr }};border:3px solid transparent;transition:border-color 0.15s;"
                              onclick="this.previousElementSibling.checked=true;document.querySelectorAll('#edit-note-form [name=color]').forEach(r=>r.parentElement.querySelector('span').style.borderColor='transparent');this.style.borderColor='white';"></span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('edit-note-modal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditNote(id, title, content, color) {
    document.getElementById('edit-note-form').action = '/notes/' + id;
    document.getElementById('edit-note-title').value = title;
    document.getElementById('edit-note-content').value = content;

    // Set color selection
    document.querySelectorAll('#edit-note-form input[name=color]').forEach(r => {
        r.checked = (r.value === color);
        r.parentElement.querySelector('span').style.borderColor = r.value === color ? 'white' : 'transparent';
    });

    document.getElementById('edit-note-modal').style.display = 'flex';
}
lucide.createIcons();
</script>
@endpush
