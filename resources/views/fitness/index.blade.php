@extends('layouts.app')

@section('title', 'Fitness Community')
@section('page-title', 'Fitness Community 🏋️')

@section('header-actions')
    <button type="button" class="btn btn-primary" onclick="document.getElementById('create-post-modal').style.display='flex'">
        <i data-lucide="plus" style="width:16px;height:16px;"></i>
        Share Workout
    </button>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:24px; max-width: 900px; margin: 0 auto;">

    <!-- Community Metric Cards -->
    <div class="grid-3" style="gap:16px;">
        <div class="glass-card stat-card animate-in">
            <div class="stat-icon" style="background:rgba(124,58,237,0.18);">
                <i data-lucide="activity" style="color:#a78bfa;width:24px;height:24px;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $totalCommunityPosts }}</div>
                <div class="stat-label">Community Posts</div>
            </div>
        </div>
        <div class="glass-card stat-card animate-in">
            <div class="stat-icon" style="background:rgba(16,185,129,0.18);">
                <i data-lucide="flame" style="color:#34d399;width:24px;height:24px;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $userPostCount }}</div>
                <div class="stat-label">Your Shared Workouts</div>
            </div>
        </div>
        <div class="glass-card stat-card animate-in">
            <div class="stat-icon" style="background:rgba(236,72,153,0.18);">
                <i data-lucide="heart" style="color:#f472b6;width:24px;height:24px;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $userLikesReceived }}</div>
                <div class="stat-label">Likes Received</div>
            </div>
        </div>
    </div>

    <!-- Filter & Category Bar -->
    <div class="glass-card animate-in" style="padding:16px 20px;">
        <form method="GET" action="{{ route('fitness.index') }}" style="display:flex; flex-direction:column; gap:14px;">
            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                <!-- Search Input -->
                <div class="input-icon-wrap" style="flex:1 1 240px; position:relative;">
                    <i data-lucide="search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); width:16px; height:16px; color:var(--text-muted); pointer-events:none;"></i>
                    <input type="text" name="search" class="form-control" placeholder="Search workouts, captions, athletes..."
                           value="{{ request('search') }}" style="padding-left:38px;">
                </div>

                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
                    @if(request('type') || request('search'))
                        <a href="{{ route('fitness.index') }}" class="btn btn-ghost btn-sm" style="color:var(--text-muted);">Reset</a>
                    @endif
                </div>
            </div>

            <!-- Workout Category Chips (Horizontal Scroll on Mobile) -->
            <div style="display:flex; gap:8px; overflow-x:auto; -webkit-overflow-scrolling:touch; padding-bottom:4px; scrollbar-width:thin;">
                @foreach($workoutTypes as $key => $label)
                    @php
                        $isActive = ($selectedType === $key) || (!$selectedType && $key === 'all');
                    @endphp
                    <a href="{{ route('fitness.index', array_merge(request()->query(), ['type' => $key === 'all' ? null : $key])) }}"
                       class="tab-btn {{ $isActive ? 'active' : '' }}"
                       style="text-decoration:none; white-space:nowrap; font-size:12px; padding:6px 14px; border-radius:20px; border:1px solid {{ $isActive ? 'transparent' : 'var(--border)' }};">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </form>
    </div>

    <!-- Feed Stream -->
    <div style="display:flex; flex-direction:column; gap:22px;">
        @forelse($posts as $post)
            <article class="glass-card animate-in" style="overflow:hidden; border-radius:var(--radius-lg);">
                <!-- Post Header -->
                <div style="padding:18px 20px 14px 20px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--border);">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->name }}"
                             style="width:42px; height:42px; border-radius:50%; object-fit:cover; border:2px solid var(--accent-purple); box-shadow:0 0 15px rgba(124,58,237,0.3);">
                        <div>
                            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                <span style="font-weight:700; color:var(--text-primary); font-size:14px;">{{ $post->user->name }}</span>
                                <span style="font-size:12px; color:var(--text-muted);">@ {{ $post->user->username_display }}</span>
                            </div>
                            <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">
                                {{ $post->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>

                    <div style="display:flex; align-items:center; gap:8px;">
                        <!-- Category Chip -->
                        <span class="badge" style="background:rgba(124,58,237,0.15); color:{{ $post->workout_badge_color }}; border:1px solid {{ $post->workout_badge_color }}40; font-size:11px;">
                            {{ ucfirst($post->workout_type) }}
                        </span>

                        @if($post->user_id === Auth::id())
                            <form action="{{ route('fitness.destroy', $post) }}" method="POST" onsubmit="return confirm('Delete this workout update?');" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon" title="Delete post" style="color:#f87171; border-color:rgba(239,68,68,0.2);">
                                    <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Workout Performance Metrics Bar -->
                @if($post->duration_minutes || $post->calories_burned)
                    <div style="display:flex; gap:14px; padding:10px 20px; background:rgba(255,255,255,0.02); border-bottom:1px solid var(--border); font-size:12px; color:var(--text-secondary); flex-wrap:wrap;">
                        @if($post->duration_minutes)
                            <div style="display:inline-flex; align-items:center; gap:6px;">
                                <i data-lucide="clock" style="width:14px; height:14px; color:#60a5fa;"></i>
                                <span>Duration: <strong>{{ $post->duration_minutes }} mins</strong></span>
                            </div>
                        @endif
                        @if($post->calories_burned)
                            <div style="display:inline-flex; align-items:center; gap:6px;">
                                <i data-lucide="flame" style="width:14px; height:14px; color:#f59e0b;"></i>
                                <span>Burned: <strong>{{ number_format($post->calories_burned) }} kcal</strong></span>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Post Body / Caption -->
                <div style="padding:18px 20px 14px 20px; font-size:14px; color:var(--text-primary); line-height:1.65; white-space:pre-wrap;">{{ $post->caption }}</div>

                <!-- Post Photo (if present) -->
                @if($post->image_path)
                    <div style="padding:0 20px 18px 20px;">
                        <div style="border-radius:var(--radius-md); overflow:hidden; border:1px solid var(--border); max-height:480px; background:#000; display:flex; align-items:center; justify-content:center;">
                            <img src="{{ $post->image_url }}" alt="Workout Photo"
                                 style="width:100%; height:auto; max-height:480px; object-fit:contain; display:block;"
                                 loading="lazy">
                        </div>
                    </div>
                @endif

                <!-- Interaction Actions (Like & Comments Count) -->
                <div style="padding:12px 20px; border-top:1px solid var(--border); background:rgba(255,255,255,0.015); display:flex; align-items:center; justify-content:space-between;"
                     x-data="{ commentsOpen: false }">
                    <div style="display:flex; align-items:center; gap:16px;">
                        <!-- Like Form Button -->
                        <form action="{{ route('fitness.like', $post) }}" method="POST" style="margin:0;">
                            @csrf
                            @php $liked = $post->isLikedBy(Auth::user()); @endphp
                            <button type="submit"
                                    class="btn-icon"
                                    style="padding:6px 12px; border-radius:20px; display:inline-flex; align-items:center; gap:6px; font-size:12.5px; border-color:{{ $liked ? 'rgba(236,72,153,0.5)' : 'var(--border)' }}; color:{{ $liked ? '#ec4899' : 'var(--text-secondary)' }}; background:{{ $liked ? 'rgba(236,72,153,0.1)' : 'transparent' }};">
                                <i data-lucide="heart" style="width:15px; height:15px; fill:{{ $liked ? '#ec4899' : 'none' }}; stroke:{{ $liked ? '#ec4899' : 'currentColor' }};"></i>
                                <span>{{ $post->likes->count() }}</span>
                            </button>
                        </form>

                        <!-- Toggle Comments Button -->
                        <button type="button" @click="commentsOpen = !commentsOpen"
                                class="btn-icon"
                                style="padding:6px 12px; border-radius:20px; display:inline-flex; align-items:center; gap:6px; font-size:12.5px;">
                            <i data-lucide="message-circle" style="width:15px; height:15px;"></i>
                            <span>{{ $post->comments->count() }} {{ Str::plural('Comment', $post->comments->count()) }}</span>
                        </button>
                    </div>

                    <span style="font-size:11.5px; color:var(--text-muted);">
                        Community Post #{{ $post->id }}
                    </span>

                    <!-- Comments Thread (Collapsible via Alpine) -->
                    <div x-show="commentsOpen" x-cloak style="width:100%; margin-top:14px; border-top:1px solid var(--border); padding-top:14px;">
                        <!-- Existing Comments List -->
                        <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:14px;">
                            @forelse($post->comments as $comment)
                                <div style="display:flex; gap:10px; padding:10px 12px; background:rgba(255,255,255,0.03); border:1px solid var(--border); border-radius:var(--radius-sm);">
                                    <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}"
                                         style="width:28px; height:28px; border-radius:50%; object-fit:cover; flex-shrink:0;">
                                    <div style="flex:1; min-width:0;">
                                        <div style="display:flex; align-items:center; justify-content:space-between; gap:8px;">
                                            <div style="font-size:12px; font-weight:700; color:var(--text-primary);">
                                                {{ $comment->user->name }}
                                                <span style="font-size:10.5px; font-weight:400; color:var(--text-muted); margin-left:4px;">
                                                    {{ $comment->created_at->diffForHumans() }}
                                                </span>
                                            </div>

                                            @if($comment->user_id === Auth::id() || $post->user_id === Auth::id())
                                                <form action="{{ route('fitness.comment.destroy', $comment) }}" method="POST" style="margin:0;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" style="background:none; border:none; color:#f87171; cursor:pointer; padding:2px;" title="Delete comment">
                                                        <i data-lucide="x" style="width:13px; height:13px;"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                        <div style="font-size:12.5px; color:var(--text-secondary); margin-top:3px; word-break:break-word;">
                                            {{ $comment->comment }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div style="font-size:12px; color:var(--text-muted); text-align:center; padding:8px 0;">
                                    No comments yet. Be the first to cheer them on!
                                </div>
                            @endforelse
                        </div>

                        <!-- Post a Comment Input Form -->
                        <form action="{{ route('fitness.comment', $post) }}" method="POST" style="display:flex; gap:8px; align-items:center;">
                            @csrf
                            <input type="text" name="comment" class="form-control" placeholder="Write a supportive comment..." required maxlength="1000" style="font-size:13px;">
                            <button type="submit" class="btn btn-primary btn-sm" style="flex-shrink:0;">
                                <i data-lucide="send" style="width:13px; height:13px;"></i>
                                Post
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="glass-card empty-state animate-in">
                <div style="width:64px; height:64px; border-radius:50%; background:rgba(124,58,237,0.15); display:flex; align-items:center; justify-content:center; margin-bottom:14px;">
                    <i data-lucide="dumbbell" style="width:32px; height:32px; color:#a78bfa;"></i>
                </div>
                <h3 style="font-size:17px; font-weight:700; color:var(--text-primary); margin-bottom:6px;">No fitness journey posts found</h3>
                <p style="font-size:13px; color:var(--text-muted); max-width:380px; margin-bottom:18px;">
                    @if(request('search') || request('type'))
                        No workouts match your current filter or query. Try resetting your search.
                    @else
                        Be the pioneer in the community! Share your run, lifting session, or wellness update.
                    @endif
                </p>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('create-post-modal').style.display='flex'">
                    <i data-lucide="plus" style="width:15px; height:15px;"></i>
                    Share First Workout
                </button>
            </div>
        @endforelse

        <!-- Pagination Links -->
        @if($posts->hasPages())
            <div style="display:flex; justify-content:center; margin-top:12px;">
                {{ $posts->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ===== SHARE WORKOUT MODAL ===== -->
<div id="create-post-modal" style="display:none;" class="modal-overlay" onclick="if(event.target===this)this.style.display='none'">
    <div class="modal-box" style="max-width:560px;">
        <div class="modal-header">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:34px; height:34px; border-radius:8px; background:var(--gradient-accent); display:flex; align-items:center; justify-content:center;">
                    <i data-lucide="flame" style="color:white; width:18px; height:18px;"></i>
                </div>
                <span class="modal-title">Share Fitness Journey</span>
            </div>
            <button type="button" class="btn-icon" onclick="document.getElementById('create-post-modal').style.display='none'">
                <i data-lucide="x" style="width:16px;height:16px;"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('fitness.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Workout Type -->
            <div class="form-group">
                <label class="form-label" for="workout_type">Workout Category *</label>
                <select name="workout_type" id="workout_type" class="form-control" required>
                    <option value="general">General Fitness</option>
                    <option value="running">Running</option>
                    <option value="weightlifting">Weightlifting / Gym</option>
                    <option value="cycling">Cycling</option>
                    <option value="hiit">HIIT / High Intensity</option>
                    <option value="yoga">Yoga & Stretching</option>
                    <option value="walking">Walking / Hiking</option>
                    <option value="swimming">Swimming</option>
                    <option value="other">Other Sports</option>
                </select>
            </div>

            <!-- Optional Metrics Row -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;" class="form-group">
                <div>
                    <label class="form-label" for="duration_minutes">Duration (Minutes)</label>
                    <div class="input-icon-wrap" style="position:relative;">
                        <i data-lucide="clock" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); width:15px; height:15px; color:var(--text-muted); pointer-events:none;"></i>
                        <input type="number" name="duration_minutes" id="duration_minutes" class="form-control" placeholder="e.g. 45" min="1" max="1440" style="padding-left:36px;">
                    </div>
                </div>
                <div>
                    <label class="form-label" for="calories_burned">Calories Burned (kcal)</label>
                    <div class="input-icon-wrap" style="position:relative;">
                        <i data-lucide="flame" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); width:15px; height:15px; color:var(--text-muted); pointer-events:none;"></i>
                        <input type="number" name="calories_burned" id="calories_burned" class="form-control" placeholder="e.g. 350" min="1" max="20000" style="padding-left:36px;">
                    </div>
                </div>
            </div>

            <!-- Photo Upload with Preview -->
            <div class="form-group">
                <label class="form-label">Workout Photo (Optional)</label>
                <div style="border:1px dashed var(--border); border-radius:var(--radius-md); padding:16px; background:rgba(255,255,255,0.02); text-align:center;">
                    <div id="post-photo-preview-wrap" style="display:none; margin-bottom:12px;">
                        <img id="post-photo-preview" src="" alt="Photo preview"
                             style="max-height:180px; max-width:100%; border-radius:var(--radius-sm); object-fit:cover; margin:0 auto; border:1px solid var(--border);">
                    </div>

                    <label class="btn btn-ghost btn-sm" style="cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                        <i data-lucide="upload" style="width:14px; height:14px;"></i>
                        <span>Select Image (JPG, PNG, WEBP max 5MB)</span>
                        <input type="file" name="image" id="fitness-image-input" accept="image/jpeg,image/png,image/webp" style="display:none;"
                               onchange="if(this.files[0]) {
                                   document.getElementById('post-photo-preview').src = URL.createObjectURL(this.files[0]);
                                   document.getElementById('post-photo-preview-wrap').style.display = 'block';
                               }">
                    </label>
                </div>
            </div>

            <!-- Caption -->
            <div class="form-group">
                <label class="form-label" for="caption">Workout Caption / Story *</label>
                <textarea name="caption" id="caption" class="form-control" rows="4" required
                          placeholder="How did today's workout feel? What milestones or PRs did you achieve? Share some inspiration!"></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('create-post-modal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="send" style="width:14px;height:14px;"></i>
                    Publish to Community
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
