@extends('layouts.app')

@section('title', 'Fitness Community')
@section('page-title', 'Fitness Community 🏋️')

@section('header-actions')
    <button type="button" class="btn btn-primary btn-share-workout"
        onclick="document.getElementById('create-post-modal').style.display='flex'">
        <i data-lucide="plus" style="width:15px;height:15px;flex-shrink:0;"></i>
        <span class="btn-share-text-full">Share Workout</span>
        <span class="btn-share-text-short">Share</span>
    </button>
@endsection

@section('content')
    <div class="fitness-container"
        style="display:flex; flex-direction:column; gap:20px; max-width:900px; width:100%; margin:0 auto; box-sizing:border-box; min-width:0;">

        <!-- Community Metric Cards -->
        <div class="grid-3 fitness-metrics-grid" style="gap:14px; width:100%; box-sizing:border-box;">
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
        <div class="glass-card animate-in fitness-filter-card"
            style="padding:14px 18px; width:100%; box-sizing:border-box;">
            <form method="GET" action="{{ route('fitness.index') }}"
                style="display:flex; flex-direction:column; gap:12px; width:100%; box-sizing:border-box;">
                <div
                    style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; width:100%; box-sizing:border-box;">
                    <!-- Search Input -->
                    <div class="input-icon-wrap"
                        style="flex:1 1 200px; min-width:0; position:relative; box-sizing:border-box;">
                        <i data-lucide="search"
                            style="position:absolute; left:12px; top:50%; transform:translateY(-50%); width:15px; height:15px; color:var(--text-muted); pointer-events:none;"></i>
                        <input type="text" name="search" class="form-control"
                            placeholder="Search workouts, captions, athletes..." value="{{ request('search') }}"
                            style="padding-left:36px; width:100%; box-sizing:border-box;">
                    </div>

                    <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                        <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
                        @if (request('type') || request('search') || $showHidden || $showHiddenComments)
                            <a href="{{ route('fitness.index') }}" class="btn btn-ghost btn-sm"
                                style="color:var(--text-muted);">Reset</a>
                        @endif

                        <!-- Hidden posts toggle -->
                        @if (!$showHidden && $hiddenCount > 0)
                            <a href="{{ route('fitness.index', array_merge(request()->query(), ['show_hidden' => 1])) }}"
                                class="btn btn-ghost btn-sm"
                                style="color:#f59e0b; border-color:rgba(245,158,11,0.3); font-size:11.5px;"
                                title="View {{ $hiddenCount }} hidden {{ Str::plural('post', $hiddenCount) }}">
                                <i data-lucide="eye-off" style="width:13px; height:13px;"></i>
                                Hidden Posts ({{ $hiddenCount }})
                            </a>
                        @endif

                        <!-- Hidden comments toggle -->
                        @if ($hiddenCommentsCount > 0)
                            <a href="{{ route('fitness.index', array_merge(request()->query(), ['show_hidden_comments' => $showHiddenComments ? null : 1])) }}"
                                class="btn btn-ghost btn-sm"
                                style="color:#f59e0b; border-color:rgba(245,158,11,0.3); font-size:11.5px;"
                                title="Toggle hidden comments">
                                <i data-lucide="eye-off" style="width:13px; height:13px;"></i>
                                {{ $showHiddenComments ? 'Hide Hidden Comments' : 'Hidden Comments (' . $hiddenCommentsCount . ')' }}
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Workout Category Chips (Horizontal Scroll on Mobile) -->
                <div
                    style="display:flex; gap:8px; overflow-x:auto; -webkit-overflow-scrolling:touch; padding-bottom:4px; scrollbar-width:thin;">
                    @foreach ($workoutTypes as $key => $label)
                        @php
                            $isActive = $selectedType === $key || (!$selectedType && $key === 'all');
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

        <!-- Hidden Posts Banner -->
        @if ($showHidden)
            <div class="glass-card animate-in"
                style="padding:14px 20px; display:flex; align-items:center; justify-content:space-between; background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.25);">
                <div style="display:flex; align-items:center; gap:10px;">
                    <i data-lucide="eye-off" style="width:18px; height:18px; color:#f59e0b;"></i>
                    <span style="font-size:13px; color:var(--text-primary); font-weight:600;">Viewing Hidden Posts</span>
                    <span style="font-size:12px; color:var(--text-muted);">These posts are hidden from your normal
                        feed.</span>
                </div>
                <a href="{{ route('fitness.index', request()->except('show_hidden')) }}" class="btn btn-ghost btn-sm"
                    style="font-size:12px;">
                    <i data-lucide="arrow-left" style="width:13px; height:13px;"></i>
                    Back to Feed
                </a>
            </div>
        @endif

        <!-- Hidden Comments Banner -->
        @if ($showHiddenComments)
            <div class="glass-card animate-in"
                style="padding:14px 20px; display:flex; align-items:center; justify-content:space-between; background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.25);">
                <div style="display:flex; align-items:center; gap:10px;">
                    <i data-lucide="message-square" style="width:18px; height:18px; color:#f59e0b;"></i>
                    <span style="font-size:13px; color:var(--text-primary); font-weight:600;">Showing Hidden Comments</span>
                    <span style="font-size:12px; color:var(--text-muted);">Previously hidden comments and replies are
                        displayed with an unhide option.</span>
                </div>
                <a href="{{ route('fitness.index', request()->except('show_hidden_comments')) }}"
                    class="btn btn-ghost btn-sm" style="font-size:12px;">
                    <i data-lucide="arrow-left" style="width:13px; height:13px;"></i>
                    Standard Comments View
                </a>
            </div>
        @endif

        <!-- Undo Toast Container (populated by JS) -->
        <div id="undo-toast-container"
            style="position:fixed; bottom:24px; left:50%; transform:translateX(-50%); z-index:9999; display:flex; flex-direction:column; gap:8px; pointer-events:none;">
        </div>

        <!-- Feed Stream -->
        <div class="fitness-feed-stream"
            style="display:flex; flex-direction:column; gap:20px; width:100%; box-sizing:border-box;">
            @forelse($posts as $post)
                <article class="glass-card animate-in post-card"
                    style="width:100%; max-width:100%; box-sizing:border-box; overflow:visible; border-radius:var(--radius-lg); position:relative;"
                    id="post-{{ $post->id }}" x-data="postActions({{ $post->id }}, {{ $post->isSavedBy(Auth::user()) ? 'true' : 'false' }})">
                    <!-- Post Header -->
                    <div class="post-header"
                        style="padding:16px 18px 12px 18px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--border); gap:10px; width:100%; box-sizing:border-box;">
                        <div style="display:flex; align-items:center; gap:10px; min-width:0; flex:1 1 auto;">
                            <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->name }}"
                                style="width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid var(--accent-purple); box-shadow:0 0 12px rgba(124,58,237,0.3); flex-shrink:0;">
                            <div style="min-width:0; flex:1 1 auto;">
                                <div style="display:flex; align-items:baseline; gap:6px; flex-wrap:wrap; min-width:0;">
                                    <span
                                        style="font-weight:700; color:var(--text-primary); font-size:13.5px; word-break:break-word;">{{ $post->user->name }}</span>
                                    <span
                                        style="font-size:11.5px; color:var(--text-muted); word-break:break-all;">{{ '@' . $post->user->username_display }}</span>
                                </div>
                                <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">
                                    {{ $post->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>

                        <!-- ===== Post Action Controls: Ellipsis Menu + X Button ===== -->
                        <div style="display:flex; align-items:center; gap:5px; flex-shrink:0;">
                            <!-- Three-Dot Ellipsis Menu -->
                            <div style="position:relative;" x-data="{ menuOpen: false }"
                                @keydown.escape.window="menuOpen = false">
                                <button type="button" class="btn-icon"
                                    style="width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,0.04); border:1px solid var(--border); transition:all 0.15s ease;"
                                    @click="menuOpen = !menuOpen; if(menuOpen) $nextTick(() => { if(typeof lucide !== 'undefined') lucide.createIcons(); })"
                                    @click.outside="menuOpen = false" :aria-expanded="menuOpen.toString()"
                                    aria-haspopup="true" aria-label="Post actions menu" title="More actions">
                                    <i data-lucide="more-vertical"
                                        style="width:15px; height:15px; color:var(--text-secondary);"></i>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="menuOpen" x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95" x-cloak role="menu"
                                    aria-label="Post actions" class="post-dropdown-menu"
                                    style="position:absolute; right:0; top:calc(100% + 6px); min-width:180px; background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-md); box-shadow:0 8px 30px rgba(0,0,0,0.4); z-index:100; padding:4px 0; backdrop-filter:blur(20px);">

                                    @if ($post->user_id === Auth::id())
                                        {{-- Author actions --}}
                                        <a href="{{ route('fitness.edit', $post) }}" role="menuitem"
                                            class="post-menu-item" @click="menuOpen = false">
                                            <i data-lucide="pencil" style="width:14px; height:14px;"></i>
                                            Edit post
                                        </a>
                                    @endif

                                    {{-- Save / Unsave --}}
                                    <button type="button" role="menuitem" class="post-menu-item"
                                        @click="toggleSave(); menuOpen = false">
                                        <i :data-lucide="isSaved ? 'bookmark-minus' : 'bookmark-plus'"
                                            style="width:14px; height:14px;"></i>
                                        <span x-text="isSaved ? 'Unsave post' : 'Save post'"></span>
                                    </button>

                                    {{-- Share --}}
                                    <button type="button" role="menuitem" class="post-menu-item"
                                        @click="sharePost(); menuOpen = false">
                                        <i data-lucide="share-2" style="width:14px; height:14px;"></i>
                                        Share post
                                    </button>

                                    {{-- Hide --}}
                                    @if (!$showHidden)
                                        <button type="button" role="menuitem" class="post-menu-item"
                                            @click="hidePost(); menuOpen = false">
                                            <i data-lucide="eye-off" style="width:14px; height:14px;"></i>
                                            Hide post
                                        </button>
                                    @endif

                                    @if ($post->user_id === Auth::id())
                                        {{-- Delete (author only) --}}
                                        <div style="border-top:1px solid var(--border); margin:4px 0;"></div>
                                        <form action="{{ route('fitness.destroy', $post) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to permanently delete this post? This action cannot be undone.');"
                                            style="margin:0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" role="menuitem" class="post-menu-item"
                                                style="color:#f87171;" @click="menuOpen = false">
                                                <i data-lucide="trash-2" style="width:14px; height:14px;"></i>
                                                Delete post
                                            </button>
                                        </form>
                                    @else
                                        {{-- Report (non-author only) --}}
                                        <div style="border-top:1px solid var(--border); margin:4px 0;"></div>
                                        <button type="button" role="menuitem" class="post-menu-item"
                                            style="color:#f59e0b;"
                                            @click="menuOpen = false; $dispatch('open-report-modal', { targetId: {{ $post->id }}, type: 'post' })">
                                            <i data-lucide="flag" style="width:14px; height:14px;"></i>
                                            Report post
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- X Button: Hide Post -->
                            @if ($showHidden)
                                <form action="{{ route('fitness.unhide', $post) }}" method="POST" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon"
                                        style="width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.25); transition:all 0.15s ease;"
                                        title="Unhide post (restore to feed)" aria-label="Unhide this post">
                                        <i data-lucide="eye" style="width:14px; height:14px; color:#34d399;"></i>
                                    </button>
                                </form>
                            @else
                                <button type="button" class="btn-icon"
                                    style="width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,0.04); border:1px solid var(--border); transition:all 0.15s ease;"
                                    @click="hidePost()" title="Hide this post from your feed"
                                    aria-label="Hide this post">
                                    <i data-lucide="x" style="width:14px; height:14px; color:var(--text-secondary);"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Workout Category + Performance Metrics Bar -->
                    <div class="post-metrics-bar"
                        style="display:flex; gap:8px 12px; padding:8px 18px; background:rgba(255,255,255,0.02); border-bottom:1px solid var(--border); font-size:11.5px; color:var(--text-secondary); flex-wrap:wrap; align-items:center; width:100%; box-sizing:border-box;">
                        <span class="badge"
                            style="background:rgba(124,58,237,0.15); color:{{ $post->workout_badge_color }}; border:1px solid {{ $post->workout_badge_color }}40; font-size:10.5px; flex-shrink:0;">
                            {{ ucfirst($post->workout_type) }}
                        </span>

                        @if ($post->duration_minutes)
                            <div style="display:inline-flex; align-items:center; gap:5px; flex-shrink:0;">
                                <i data-lucide="clock" style="width:13px; height:13px; color:#60a5fa;"></i>
                                <span>Duration: <strong>{{ $post->duration_minutes }}m</strong></span>
                            </div>
                        @endif
                        @if ($post->calories_burned)
                            <div style="display:inline-flex; align-items:center; gap:5px; flex-shrink:0;">
                                <i data-lucide="flame" style="width:13px; height:13px; color:#f59e0b;"></i>
                                <span>Burned: <strong>{{ number_format($post->calories_burned) }} kcal</strong></span>
                            </div>
                        @endif
                    </div>

                    <!-- Post Body / Caption -->
                    <div class="post-caption"
                        style="padding:14px 18px 12px 18px; font-size:13.5px; color:var(--text-primary); line-height:1.6; white-space:pre-wrap; word-break:break-word; overflow-wrap:anywhere; width:100%; box-sizing:border-box;">
                        {{ $post->caption }}</div>

                    <!-- Post Photo (if present) -->
                    @if ($post->image_path)
                        <div class="post-image-wrap" style="padding:0 18px 14px 18px; width:100%; box-sizing:border-box;">
                            <div
                                style="border-radius:var(--radius-md); overflow:hidden; border:1px solid var(--border); max-height:480px; background:#000; display:flex; align-items:center; justify-content:center; width:100%; box-sizing:border-box;">
                                <img src="{{ $post->image_url }}" alt="Workout Photo"
                                    style="width:100%; max-width:100%; height:auto; max-height:480px; object-fit:contain; display:block;"
                                    loading="lazy">
                            </div>
                        </div>
                    @endif

                    <!-- Interaction Actions (Like & Comments Count) and Comments Section -->
                    <div class="post-interactions-wrap"
                        style="border-top:1px solid var(--border); background:rgba(255,255,255,0.015); width:100%; box-sizing:border-box;"
                        x-data="{ commentsOpen: false }">
                        <!-- Actions Bar -->
                        <div class="post-actions-bar"
                            style="padding:10px 18px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; width:100%; box-sizing:border-box;">
                            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; min-width:0;">
                                <!-- Like Form Button -->
                                <form action="{{ route('fitness.like', $post) }}" method="POST" style="margin:0;">
                                    @csrf
                                    @php $liked = $post->isLikedBy(Auth::user()); @endphp
                                    <button type="submit" class="btn-icon"
                                        style="padding:6px 12px; border-radius:20px; display:inline-flex; align-items:center; gap:6px; font-size:12px; border-color:{{ $liked ? 'rgba(236,72,153,0.5)' : 'var(--border)' }}; color:{{ $liked ? '#ec4899' : 'var(--text-secondary)' }}; background:{{ $liked ? 'rgba(236,72,153,0.1)' : 'transparent' }};">
                                        <i data-lucide="heart"
                                            style="width:14px; height:14px; fill:{{ $liked ? '#ec4899' : 'none' }}; stroke:{{ $liked ? '#ec4899' : 'currentColor' }};"></i>
                                        <span>{{ $post->likes->count() }}</span>
                                    </button>
                                </form>

                                <!-- Toggle Comments Button -->
                                @php
                                    $totalCommentsCount =
                                        $post->comments_count ??
                                        $post->comments->sum(fn($c) => 1 + ($c->replies ? $c->replies->count() : 0));
                                @endphp
                                <button type="button"
                                    @click="commentsOpen = !commentsOpen; if(commentsOpen) $nextTick(() => { if(typeof lucide !== 'undefined') lucide.createIcons(); })"
                                    class="btn-icon"
                                    style="padding:6px 12px; border-radius:20px; display:inline-flex; align-items:center; gap:6px; font-size:12px;">
                                    <i data-lucide="message-circle" style="width:14px; height:14px;"></i>
                                    <span id="comments-count-{{ $post->id }}">{{ $totalCommentsCount }}
                                        {{ Str::plural('Comment', $totalCommentsCount) }}</span>
                                </button>

                                <!-- Saved indicator -->
                                <span x-show="isSaved" x-cloak
                                    style="display:inline-flex; align-items:center; gap:4px; font-size:11px; color:#a78bfa;">
                                    <i data-lucide="bookmark" style="width:12px; height:12px; fill:#a78bfa;"></i>
                                    Saved
                                </span>
                            </div>
                        </div>

                        <!-- Comments Thread (Collapsible via Alpine) -->
                        <div x-show="commentsOpen" x-cloak class="post-comments-thread"
                            style="width:100%; max-width:100%; box-sizing:border-box; border-top:1px solid var(--border); padding:14px 18px;">
                            <!-- Top-Level Comments List -->
                            <div
                                style="display:flex; flex-direction:column; gap:12px; margin-bottom:14px; overflow:visible;">
                                @forelse($post->comments as $comment)
                                    @php
                                        $isCommentAuthor = $comment->user_id === Auth::id();
                                        $isPostOwner = $post->user_id === Auth::id();
                                        $isCommentHidden = $comment->isHiddenBy(Auth::user());
                                        $userReaction = $comment->userReaction(Auth::user());
                                        $reactionCounts = $comment->reactionCounts();
                                        $totalReactions = $comment->totalReactions();
                                    @endphp
                                    <div class="comment-item-card" id="comment-{{ $comment->id }}"
                                        x-data="commentActions({{ $comment->id }}, {{ $post->id }}, {{ json_encode($comment->comment) }}, {{ $comment->isEdited() ? 'true' : 'false' }}, {{ $isCommentHidden ? 'true' : 'false' }}, {{ json_encode($userReaction) }}, {{ json_encode($reactionCounts) }}, {{ $totalReactions }}, {{ json_encode($comment->user->name) }})"
                                        style="display:flex; flex-direction:column; gap:6px; padding:10px 12px; background:rgba(255,255,255,0.03); border:1px solid var(--border); border-radius:var(--radius-sm); position:relative; overflow:visible; width:100%; max-width:100%; box-sizing:border-box; min-width:0;">

                                        <div
                                            style="display:flex; gap:8px; align-items:flex-start; position:relative; overflow:visible; width:100%; box-sizing:border-box; min-width:0;">
                                            <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}"
                                                style="width:28px; height:28px; border-radius:50%; object-fit:cover; flex-shrink:0; margin-top:2px;">

                                            <div
                                                style="flex:1 1 0; min-width:0; position:relative; overflow:visible; width:100%; box-sizing:border-box;">
                                                <!-- Comment Header -->
                                                <div
                                                    style="display:flex; align-items:flex-start; justify-content:space-between; gap:6px; width:100%;">
                                                    <div
                                                        style="display:flex; align-items:baseline; gap:5px; flex-wrap:wrap; min-width:0; flex:1 1 auto;">
                                                        <span
                                                            style="font-size:12px; font-weight:700; color:var(--text-primary); word-break:break-word;">{{ $comment->user->name }}</span>
                                                        <span
                                                            style="font-size:10.5px; font-weight:400; color:var(--text-muted); flex-shrink:0;">
                                                            {{ $comment->created_at->diffForHumans() }}
                                                        </span>
                                                        <!-- Edited indicator -->
                                                        <span x-show="isEdited" x-cloak
                                                            style="font-size:10px; color:var(--text-muted); font-style:italic;"
                                                            title="Comment was edited">(edited)</span>
                                                        @if ($comment->isEdited())
                                                            <span x-show="!isEdited"
                                                                style="font-size:10px; color:var(--text-muted); font-style:italic;">(edited)</span>
                                                        @endif
                                                        <!-- Hidden badge -->
                                                        <span x-show="isHidden" class="badge"
                                                            style="background:rgba(245,158,11,0.15); color:#f59e0b; font-size:10px; border:1px solid rgba(245,158,11,0.3); padding:1px 6px;">
                                                            Hidden by you
                                                        </span>
                                                    </div>

                                                    <!-- Comment Controls (Three-dot & X) -->
                                                    <div
                                                        style="display:flex; align-items:center; gap:4px; position:relative; flex-shrink:0;">
                                                        <!-- Three-Dot Menu -->
                                                        <div style="position:relative;"
                                                            @keydown.escape.window="menuOpen = false">
                                                            <button type="button" class="btn-icon"
                                                                style="width:26px; height:26px; border-radius:6px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,0.04); border:1px solid var(--border); transition:all 0.15s ease;"
                                                                @click="menuOpen = !menuOpen; if(menuOpen) $nextTick(() => { if(typeof lucide !== 'undefined') lucide.createIcons(); })"
                                                                @click.outside="menuOpen = false"
                                                                :aria-expanded="menuOpen.toString()" aria-haspopup="true"
                                                                aria-label="Comment actions" title="Comment actions">
                                                                <i data-lucide="more-vertical"
                                                                    style="width:13px; height:13px; color:var(--text-secondary);"></i>
                                                            </button>

                                                            <!-- Dropdown Menu -->
                                                            <div x-show="menuOpen"
                                                                x-transition:enter="transition ease-out duration-100"
                                                                x-transition:enter-start="opacity-0 scale-95"
                                                                x-transition:enter-end="opacity-100 scale-100"
                                                                x-transition:leave="transition ease-in duration-75"
                                                                x-transition:leave-start="opacity-100 scale-100"
                                                                x-transition:leave-end="opacity-0 scale-95" x-cloak
                                                                role="menu" aria-label="Comment action menu"
                                                                style="position:absolute; right:0; top:calc(100% + 4px); min-width:160px; background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-md); box-shadow:0 8px 30px rgba(0,0,0,0.4); z-index:100; padding:4px 0; backdrop-filter:blur(20px);">

                                                                @if ($isCommentAuthor)
                                                                    <button type="button" role="menuitem"
                                                                        class="comment-menu-item" @click="startEdit()">
                                                                        <i data-lucide="pencil"
                                                                            style="width:13px; height:13px;"></i>
                                                                        Edit comment
                                                                    </button>
                                                                @endif

                                                                <button type="button" role="menuitem"
                                                                    class="comment-menu-item"
                                                                    @click="copyComment(); menuOpen = false">
                                                                    <i data-lucide="copy"
                                                                        style="width:13px; height:13px;"></i>
                                                                    Copy comment
                                                                </button>

                                                                <button type="button" role="menuitem"
                                                                    class="comment-menu-item"
                                                                    @click="isHidden ? unhideComment() : hideComment(); menuOpen = false">
                                                                    <i :data-lucide="isHidden ? 'eye' : 'eye-off'"
                                                                        style="width:13px; height:13px;"></i>
                                                                    <span
                                                                        x-text="isHidden ? 'Unhide comment' : 'Hide comment'"></span>
                                                                </button>

                                                                <button type="button" role="menuitem"
                                                                    class="comment-menu-item" style="color:#f59e0b;"
                                                                    @click="menuOpen = false; $dispatch('open-report-modal', { targetId: {{ $comment->id }}, type: 'comment' })">
                                                                    <i data-lucide="flag"
                                                                        style="width:13px; height:13px;"></i>
                                                                    Report comment
                                                                </button>

                                                                @if ($isCommentAuthor || $isPostOwner)
                                                                    <div
                                                                        style="border-top:1px solid var(--border); margin:4px 0;">
                                                                    </div>
                                                                    <button type="button" role="menuitem"
                                                                        class="comment-menu-item" style="color:#f87171;"
                                                                        @click="deleteComment(); menuOpen = false">
                                                                        <i data-lucide="trash-2"
                                                                            style="width:13px; height:13px;"></i>
                                                                        Delete comment
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <!-- X Button (Hide or Unhide) -->
                                                        <button type="button" class="btn-icon"
                                                            style="width:26px; height:26px; border-radius:6px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,0.04); border:1px solid var(--border); transition:all 0.15s ease;"
                                                            @click="isHidden ? unhideComment() : hideComment()"
                                                            :title="isHidden ? 'Unhide comment' : 'Hide comment from your view'"
                                                            :aria-label="isHidden ? 'Unhide comment' : 'Hide comment'">
                                                            <i :data-lucide="isHidden ? 'eye' : 'x'"
                                                                style="width:13px; height:13px; color:var(--text-secondary);"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Display Comment Text -->
                                                <div x-show="!editing"
                                                    style="font-size:12.5px; color:var(--text-secondary); margin-top:3px; word-break:break-word;">
                                                    <span x-text="commentText">{{ $comment->comment }}</span>
                                                </div>

                                                <!-- Inline Comment Editor -->
                                                <div x-show="editing" x-cloak style="margin-top:6px;">
                                                    <form @submit.prevent="saveEdit()">
                                                        <textarea x-model="editText" class="form-control" rows="2" maxlength="1000" required
                                                            placeholder="Edit your comment..."
                                                            style="font-size:12.5px; padding:8px 10px; width:100%; border-radius:var(--radius-sm);"></textarea>
                                                        <div
                                                            style="display:flex; justify-content:flex-end; gap:6px; margin-top:6px;">
                                                            <button type="button" class="btn btn-ghost btn-sm"
                                                                @click="cancelEdit()"
                                                                style="padding:4px 10px; font-size:11.5px;">Cancel</button>
                                                            <button type="submit" class="btn btn-primary btn-sm"
                                                                :disabled="isSaving || !editText.trim()"
                                                                style="padding:4px 10px; font-size:11.5px;">
                                                                <span x-text="isSaving ? 'Saving...' : 'Save'"></span>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>

                                                <!-- Comment Action Footer: Reactions & Reply -->
                                                <div
                                                    style="display:flex; align-items:center; gap:8px; margin-top:8px; flex-wrap:wrap; position:relative;">
                                                    <!-- Reaction Picker Popover -->
                                                    <div style="position:relative;" @click.outside="pickerOpen = false"
                                                        @keydown.escape.window="pickerOpen = false">
                                                        <button type="button" class="comment-action-pill"
                                                            :class="{ 'active': userReaction }"
                                                            @click="pickerOpen = !pickerOpen"
                                                            title="React to this comment"
                                                            aria-label="React to this comment">
                                                            <span x-text="reactionEmoji(userReaction) || '👍'"
                                                                style="font-size:12px;"></span>
                                                            <span
                                                                x-text="userReaction ? capitalize(userReaction) : 'React'"
                                                                style="font-size:11px; font-weight:600;"></span>
                                                        </button>

                                                        <!-- Popover -->
                                                        <div x-show="pickerOpen"
                                                            x-transition:enter="transition ease-out duration-150"
                                                            x-transition:enter-start="opacity-0 translate-y-1 scale-90"
                                                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                            x-transition:leave="transition ease-in duration-100"
                                                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                            x-transition:leave-end="opacity-0 translate-y-1 scale-90"
                                                            x-cloak class="reaction-popover"
                                                            style="position:absolute; bottom:calc(100% + 4px); left:0; background:var(--bg-card); border:1px solid var(--border); border-radius:24px; box-shadow:0 8px 30px rgba(0,0,0,0.5); backdrop-filter:blur(24px); z-index:120; display:flex; gap:4px; padding:3px 6px;">
                                                            <button type="button" @click="toggleReaction('like')"
                                                                class="reaction-emoji-btn"
                                                                :class="{ 'selected': userReaction === 'like' }"
                                                                title="Like (👍)">👍</button>
                                                            <button type="button" @click="toggleReaction('love')"
                                                                class="reaction-emoji-btn"
                                                                :class="{ 'selected': userReaction === 'love' }"
                                                                title="Love (❤️)">❤️</button>
                                                            <button type="button" @click="toggleReaction('celebrate')"
                                                                class="reaction-emoji-btn"
                                                                :class="{ 'selected': userReaction === 'celebrate' }"
                                                                title="Celebrate (🎉)">🎉</button>
                                                            <button type="button" @click="toggleReaction('laugh')"
                                                                class="reaction-emoji-btn"
                                                                :class="{ 'selected': userReaction === 'laugh' }"
                                                                title="Laugh (😂)">😂</button>
                                                            <button type="button" @click="toggleReaction('fire')"
                                                                class="reaction-emoji-btn"
                                                                :class="{ 'selected': userReaction === 'fire' }"
                                                                title="Fire (🔥)">🔥</button>
                                                        </div>
                                                    </div>

                                                    <!-- Reaction badges summary -->
                                                    <div
                                                        style="display:flex; align-items:center; gap:4px; flex-wrap:wrap;">
                                                        <template x-for="(count, type) in reactionCounts"
                                                            :key="type">
                                                            <button type="button" x-show="count > 0"
                                                                @click="toggleReaction(type)" class="reaction-badge-pill"
                                                                :class="{ 'user-reacted': userReaction === type }"
                                                                :title="capitalize(type) + ' (' + count + ')'">
                                                                <span x-text="reactionEmoji(type)"></span>
                                                                <span x-text="count"
                                                                    style="font-size:11px; font-weight:600;"></span>
                                                            </button>
                                                        </template>
                                                    </div>

                                                    <!-- Reply Button -->
                                                    <button type="button" class="comment-action-pill"
                                                        @click="openReply()" title="Reply to this comment"
                                                        aria-label="Reply to this comment">
                                                        <i data-lucide="reply" style="width:12px; height:12px;"></i>
                                                        <span style="font-size:11px; font-weight:600;">Reply</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Inline Reply Composer -->
                                        <div x-show="replying" x-cloak
                                            style="margin-top:6px; padding:10px 12px; background:rgba(124,58,237,0.06); border:1px solid rgba(124,58,237,0.25); border-radius:var(--radius-sm);">
                                            <div
                                                style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px;">
                                                <span
                                                    style="font-size:11px; color:#a78bfa; font-weight:600; display:flex; align-items:center; gap:4px;">
                                                    <i data-lucide="corner-down-right"
                                                        style="width:12px; height:12px;"></i>
                                                    Replying to <span x-text="replyingToName"></span>
                                                </span>
                                                <button type="button" @click="cancelReply()"
                                                    style="background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:11px;">Cancel</button>
                                            </div>
                                            <form action="{{ route('fitness.comment.reply', $comment) }}" method="POST"
                                                @submit="isSubmittingReply = true">
                                                @csrf
                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                <textarea name="comment" x-model="replyText" class="form-control" rows="2" maxlength="1000" required
                                                    placeholder="Write your reply..."
                                                    style="font-size:12px; padding:8px 10px; width:100%; border-radius:var(--radius-sm);"></textarea>
                                                <div
                                                    style="display:flex; justify-content:flex-end; gap:6px; margin-top:6px;">
                                                    <button type="button" class="btn btn-ghost btn-sm"
                                                        @click="cancelReply()"
                                                        style="padding:3px 10px; font-size:11.5px;">Cancel</button>
                                                    <button type="submit" class="btn btn-primary btn-sm"
                                                        :disabled="isSubmittingReply || !replyText.trim()"
                                                        style="padding:3px 12px; font-size:11.5px;">
                                                        <i data-lucide="send" style="width:11px; height:11px;"></i>
                                                        <span x-text="isSubmittingReply ? 'Posting...' : 'Reply'"></span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Replies List (Threaded with Indentation & Connector) -->
                                        @if ($comment->replies && $comment->replies->count() > 0)
                                            <div style="margin-top:4px;">
                                                <button type="button"
                                                    @click="repliesOpen = !repliesOpen; if(repliesOpen) $nextTick(() => { if(typeof lucide !== 'undefined') lucide.createIcons(); })"
                                                    class="btn-link"
                                                    style="font-size:11.5px; color:#a78bfa; display:inline-flex; align-items:center; gap:4px; margin-bottom:6px; cursor:pointer; background:none; border:none; padding:2px 0;">
                                                    <i :data-lucide="repliesOpen ? 'chevron-up' : 'chevron-down'"
                                                        style="width:12px; height:12px;"></i>
                                                    <span
                                                        x-text="repliesOpen ? 'Hide replies ({{ $comment->replies->count() }})' : 'Show {{ $comment->replies->count() }} {{ Str::plural('reply', $comment->replies->count()) }}'"></span>
                                                </button>

                                                <div x-show="repliesOpen" x-cloak class="replies-thread-container">
                                                    @foreach ($comment->replies as $reply)
                                                        @php
                                                            $isReplyAuthor = $reply->user_id === Auth::id();
                                                            $isReplyHidden = $reply->isHiddenBy(Auth::user());
                                                            $replyUserReaction = $reply->userReaction(Auth::user());
                                                            $replyReactionCounts = $reply->reactionCounts();
                                                            $replyTotalReactions = $reply->totalReactions();
                                                        @endphp
                                                        <div class="comment-item-card reply-item-card"
                                                            id="comment-{{ $reply->id }}" x-data="commentActions({{ $reply->id }}, {{ $post->id }}, {{ json_encode($reply->comment) }}, {{ $reply->isEdited() ? 'true' : 'false' }}, {{ $isReplyHidden ? 'true' : 'false' }}, {{ json_encode($replyUserReaction) }}, {{ json_encode($replyReactionCounts) }}, {{ $replyTotalReactions }}, {{ json_encode($reply->user->name) }})"
                                                            style="display:flex; flex-direction:column; gap:5px; padding:8px 10px; background:rgba(255,255,255,0.02); border:1px solid var(--border); border-radius:var(--radius-sm); position:relative; overflow:visible; width:100%; max-width:100%; box-sizing:border-box; min-width:0;">

                                                            <div
                                                                style="display:flex; gap:6px; align-items:flex-start; position:relative; overflow:visible; width:100%; box-sizing:border-box; min-width:0;">
                                                                <img src="{{ $reply->user->avatar_url }}"
                                                                    alt="{{ $reply->user->name }}"
                                                                    style="width:22px; height:22px; border-radius:50%; object-fit:cover; flex-shrink:0; margin-top:2px;">

                                                                <div
                                                                    style="flex:1 1 0; min-width:0; position:relative; overflow:visible; width:100%; box-sizing:border-box;">
                                                                    <!-- Reply Header -->
                                                                    <div
                                                                        style="display:flex; align-items:flex-start; justify-content:space-between; gap:4px; width:100%;">
                                                                        <div
                                                                            style="display:flex; align-items:baseline; gap:4px; flex-wrap:wrap; min-width:0; flex:1 1 auto;">
                                                                            <span
                                                                                style="font-size:11.5px; font-weight:700; color:var(--text-primary); word-break:break-word;">{{ $reply->user->name }}</span>
                                                                            <span
                                                                                style="font-size:10px; color:var(--text-muted); flex-shrink:0;">{{ $reply->created_at->diffForHumans() }}</span>
                                                                            <span x-show="isEdited" x-cloak
                                                                                style="font-size:9.5px; color:var(--text-muted); font-style:italic;">(edited)</span>
                                                                            @if ($reply->isEdited())
                                                                                <span x-show="!isEdited"
                                                                                    style="font-size:9.5px; color:var(--text-muted); font-style:italic;">(edited)</span>
                                                                            @endif
                                                                            <span x-show="isHidden" class="badge"
                                                                                style="background:rgba(245,158,11,0.15); color:#f59e0b; font-size:9.5px; padding:1px 5px;">Hidden</span>
                                                                        </div>

                                                                        <!-- Controls -->
                                                                        <div
                                                                            style="display:flex; align-items:center; gap:2px; flex-shrink:0; position:relative;">
                                                                            <!-- Three-dot -->
                                                                            <div style="position:relative;"
                                                                                @keydown.escape.window="menuOpen = false">
                                                                                <button type="button" class="btn-icon"
                                                                                    style="width:24px; height:24px; border-radius:6px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,0.04); border:1px solid var(--border);"
                                                                                    @click="menuOpen = !menuOpen; if(menuOpen) $nextTick(() => { if(typeof lucide !== 'undefined') lucide.createIcons(); })"
                                                                                    @click.outside="menuOpen = false"
                                                                                    title="Reply actions">
                                                                                    <i data-lucide="more-vertical"
                                                                                        style="width:12px; height:12px; color:var(--text-secondary);"></i>
                                                                                </button>
                                                                                <div x-show="menuOpen" x-cloak
                                                                                    class="comment-dropdown-menu"
                                                                                    style="position:absolute; right:0; top:calc(100% + 4px); min-width:150px; background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-md); box-shadow:0 8px 30px rgba(0,0,0,0.4); z-index:110; padding:4px 0; backdrop-filter:blur(20px);">
                                                                                    @if ($isReplyAuthor)
                                                                                        <button type="button"
                                                                                            class="comment-menu-item"
                                                                                            @click="startEdit()">
                                                                                            <i data-lucide="pencil"
                                                                                                style="width:12px; height:12px;"></i>
                                                                                            Edit reply
                                                                                        </button>
                                                                                    @endif
                                                                                    <button type="button"
                                                                                        class="comment-menu-item"
                                                                                        @click="copyComment(); menuOpen = false">
                                                                                        <i data-lucide="copy"
                                                                                            style="width:12px; height:12px;"></i>
                                                                                        Copy reply
                                                                                    </button>
                                                                                    <button type="button"
                                                                                        class="comment-menu-item"
                                                                                        @click="isHidden ? unhideComment() : hideComment(); menuOpen = false">
                                                                                        <i :data-lucide="isHidden ? 'eye' : 'eye-off'"
                                                                                            style="width:12px; height:12px;"></i>
                                                                                        <span
                                                                                            x-text="isHidden ? 'Unhide reply' : 'Hide reply'"></span>
                                                                                    </button>
                                                                                    <button type="button"
                                                                                        class="comment-menu-item"
                                                                                        style="color:#f59e0b;"
                                                                                        @click="menuOpen = false; $dispatch('open-report-modal', { targetId: {{ $reply->id }}, type: 'comment' })">
                                                                                        <i data-lucide="flag"
                                                                                            style="width:12px; height:12px;"></i>
                                                                                        Report reply
                                                                                    </button>
                                                                                    @if ($isReplyAuthor || $isPostOwner)
                                                                                        <div
                                                                                            style="border-top:1px solid var(--border); margin:4px 0;">
                                                                                        </div>
                                                                                        <button type="button"
                                                                                            class="comment-menu-item"
                                                                                            style="color:#f87171;"
                                                                                            @click="deleteComment(); menuOpen = false">
                                                                                            <i data-lucide="trash-2"
                                                                                                style="width:12px; height:12px;"></i>
                                                                                            Delete reply
                                                                                        </button>
                                                                                    @endif
                                                                                </div>
                                                                            </div>

                                                                            <!-- X Button -->
                                                                            <button type="button" class="btn-icon"
                                                                                style="width:24px; height:24px; border-radius:6px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,0.04); border:1px solid var(--border);"
                                                                                @click="isHidden ? unhideComment() : hideComment()"
                                                                                :title="isHidden ? 'Unhide reply' : 'Hide reply'">
                                                                                <i :data-lucide="isHidden ? 'eye' : 'x'"
                                                                                    style="width:12px; height:12px; color:var(--text-secondary);"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Reply Text -->
                                                                    <div x-show="!editing"
                                                                        style="font-size:12px; color:var(--text-secondary); margin-top:2px; word-break:break-word;">
                                                                        <span
                                                                            x-text="commentText">{{ $reply->comment }}</span>
                                                                    </div>

                                                                    <!-- Inline Edit -->
                                                                    <div x-show="editing" x-cloak style="margin-top:6px;">
                                                                        <form @submit.prevent="saveEdit()">
                                                                            <textarea x-model="editText" class="form-control" rows="2" maxlength="1000" required
                                                                                style="font-size:12px; padding:6px 8px; width:100%; border-radius:var(--radius-sm);"></textarea>
                                                                            <div
                                                                                style="display:flex; justify-content:flex-end; gap:6px; margin-top:4px;">
                                                                                <button type="button"
                                                                                    class="btn btn-ghost btn-sm"
                                                                                    @click="cancelEdit()"
                                                                                    style="padding:2px 8px; font-size:11px;">Cancel</button>
                                                                                <button type="submit"
                                                                                    class="btn btn-primary btn-sm"
                                                                                    :disabled="isSaving || !editText.trim()"
                                                                                    style="padding:2px 10px; font-size:11px;">
                                                                                    <span
                                                                                        x-text="isSaving ? 'Saving...' : 'Save'"></span>
                                                                                </button>
                                                                            </div>
                                                                        </form>
                                                                    </div>

                                                                    <!-- Reply Action Footer: Reactions & Reply -->
                                                                    <div
                                                                        style="display:flex; align-items:center; gap:8px; margin-top:6px; flex-wrap:wrap; position:relative;">
                                                                        <!-- Reaction Picker -->
                                                                        <div style="position:relative;"
                                                                            @click.outside="pickerOpen = false"
                                                                            @keydown.escape.window="pickerOpen = false">
                                                                            <button type="button"
                                                                                class="comment-action-pill"
                                                                                :class="{ 'active': userReaction }"
                                                                                @click="pickerOpen = !pickerOpen"
                                                                                title="React">
                                                                                <span
                                                                                    x-text="reactionEmoji(userReaction) || '👍'"
                                                                                    style="font-size:12px;"></span>
                                                                                <span
                                                                                    x-text="userReaction ? capitalize(userReaction) : 'React'"
                                                                                    style="font-size:10.5px;"></span>
                                                                            </button>
                                                                            <div x-show="pickerOpen" x-cloak
                                                                                class="reaction-popover"
                                                                                style="position:absolute; bottom:calc(100% + 4px); left:0; background:var(--bg-card); border:1px solid var(--border); border-radius:20px; box-shadow:0 6px 20px rgba(0,0,0,0.5); backdrop-filter:blur(20px); z-index:120; display:flex; gap:4px; padding:3px 6px;">
                                                                                <button type="button"
                                                                                    @click="toggleReaction('like')"
                                                                                    class="reaction-emoji-btn"
                                                                                    :class="{ 'selected': userReaction === 'like' }"
                                                                                    title="Like">👍</button>
                                                                                <button type="button"
                                                                                    @click="toggleReaction('love')"
                                                                                    class="reaction-emoji-btn"
                                                                                    :class="{ 'selected': userReaction === 'love' }"
                                                                                    title="Love">❤️</button>
                                                                                <button type="button"
                                                                                    @click="toggleReaction('celebrate')"
                                                                                    class="reaction-emoji-btn"
                                                                                    :class="{ 'selected': userReaction === 'celebrate' }"
                                                                                    title="Celebrate">🎉</button>
                                                                                <button type="button"
                                                                                    @click="toggleReaction('laugh')"
                                                                                    class="reaction-emoji-btn"
                                                                                    :class="{ 'selected': userReaction === 'laugh' }"
                                                                                    title="Laugh">😂</button>
                                                                                <button type="button"
                                                                                    @click="toggleReaction('fire')"
                                                                                    class="reaction-emoji-btn"
                                                                                    :class="{ 'selected': userReaction === 'fire' }"
                                                                                    title="Fire">🔥</button>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Reaction counts -->
                                                                        <div
                                                                            style="display:flex; align-items:center; gap:4px; flex-wrap:wrap;">
                                                                            <template
                                                                                x-for="(count, type) in reactionCounts"
                                                                                :key="type">
                                                                                <button type="button" x-show="count > 0"
                                                                                    @click="toggleReaction(type)"
                                                                                    class="reaction-badge-pill"
                                                                                    :class="{ 'user-reacted': userReaction ===
                                                                                            type }">
                                                                                    <span
                                                                                        x-text="reactionEmoji(type)"></span>
                                                                                    <span x-text="count"
                                                                                        style="font-size:10.5px;"></span>
                                                                                </button>
                                                                            </template>
                                                                        </div>

                                                                        <!-- Reply button on reply -> activates root comment reply composer targeting this user -->
                                                                        <button type="button" class="comment-action-pill"
                                                                            @click="$dispatch('open-reply-composer', { commentId: {{ $comment->id }}, mentionName: '{{ $reply->user->name }}' })"
                                                                            title="Reply to {{ $reply->user->name }}">
                                                                            <i data-lucide="reply"
                                                                                style="width:11px; height:11px;"></i>
                                                                            <span style="font-size:10.5px;">Reply</span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div
                                        style="font-size:12px; color:var(--text-muted); text-align:center; padding:8px 0;">
                                        No comments yet. Be the first to cheer them on!
                                    </div>
                                @endforelse
                            </div>

                            <!-- Post a Comment Input Form -->
                            <form action="{{ route('fitness.comment', $post) }}" method="POST"
                                style="display:flex; gap:8px; align-items:center; width:100%; box-sizing:border-box;">
                                @csrf
                                <input type="text" name="comment" class="form-control"
                                    placeholder="Write a supportive comment..." required maxlength="1000"
                                    style="font-size:12.5px; flex:1 1 0; min-width:0; box-sizing:border-box;">
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
                    <div
                        style="width:64px; height:64px; border-radius:50%; background:rgba(124,58,237,0.15); display:flex; align-items:center; justify-content:center; margin-bottom:14px;">
                        <i data-lucide="dumbbell" style="width:32px; height:32px; color:#a78bfa;"></i>
                    </div>
                    <h3 style="font-size:17px; font-weight:700; color:var(--text-primary); margin-bottom:6px;">
                        @if ($showHidden)
                            No hidden posts
                        @else
                            No fitness journey posts found
                        @endif
                    </h3>
                    <p style="font-size:13px; color:var(--text-muted); max-width:380px; margin-bottom:18px;">
                        @if ($showHidden)
                            You haven't hidden any posts. All posts are visible in your feed.
                        @elseif(request('search') || request('type'))
                            No workouts match your current filter or query. Try resetting your search.
                        @else
                            Be the pioneer in the community! Share your run, lifting session, or wellness update.
                        @endif
                    </p>
                    @if (!$showHidden)
                        <button type="button" class="btn btn-primary"
                            onclick="document.getElementById('create-post-modal').style.display='flex'">
                            <i data-lucide="plus" style="width:15px; height:15px;"></i>
                            Share First Workout
                        </button>
                    @else
                        <a href="{{ route('fitness.index') }}" class="btn btn-primary">
                            <i data-lucide="arrow-left" style="width:15px; height:15px;"></i>
                            Back to Feed
                        </a>
                    @endif
                </div>
            @endforelse

            <!-- Pagination Links -->
            @if ($posts->hasPages())
                <div style="display:flex; justify-content:center; margin-top:12px;">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- ===== SHARE WORKOUT MODAL ===== -->
    <div id="create-post-modal" style="display:none;" class="modal-overlay"
        onclick="if(event.target===this)this.style.display='none'">
        <div class="modal-box" style="max-width:560px;">
            <div class="modal-header">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div
                        style="width:34px; height:34px; border-radius:8px; background:var(--gradient-accent); display:flex; align-items:center; justify-content:center;">
                        <i data-lucide="flame" style="color:white; width:18px; height:18px;"></i>
                    </div>
                    <span class="modal-title">Share Fitness Journey</span>
                </div>
                <button type="button" class="btn-icon"
                    onclick="document.getElementById('create-post-modal').style.display='none'">
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
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;" class="form-group modal-metrics-grid">
                    <div>
                        <label class="form-label" for="duration_minutes">Duration (Minutes)</label>
                        <div class="input-icon-wrap" style="position:relative;">
                            <i data-lucide="clock"
                                style="position:absolute; left:12px; top:50%; transform:translateY(-50%); width:15px; height:15px; color:var(--text-muted); pointer-events:none;"></i>
                            <input type="number" name="duration_minutes" id="duration_minutes" class="form-control"
                                placeholder="e.g. 45" min="1" max="1440" style="padding-left:36px;">
                        </div>
                    </div>
                    <div>
                        <label class="form-label" for="calories_burned">Calories Burned (kcal)</label>
                        <div class="input-icon-wrap" style="position:relative;">
                            <i data-lucide="flame"
                                style="position:absolute; left:12px; top:50%; transform:translateY(-50%); width:15px; height:15px; color:var(--text-muted); pointer-events:none;"></i>
                            <input type="number" name="calories_burned" id="calories_burned" class="form-control"
                                placeholder="e.g. 350" min="1" max="20000" style="padding-left:36px;">
                        </div>
                    </div>
                </div>

                <!-- Photo Upload with Preview -->
                <div class="form-group">
                    <label class="form-label">Workout Photo (Optional)</label>
                    <div
                        style="border:1px dashed var(--border); border-radius:var(--radius-md); padding:16px; background:rgba(255,255,255,0.02); text-align:center;">
                        <div id="post-photo-preview-wrap" style="display:none; margin-bottom:12px;">
                            <img id="post-photo-preview" src="" alt="Photo preview"
                                style="max-height:180px; max-width:100%; border-radius:var(--radius-sm); object-fit:cover; margin:0 auto; border:1px solid var(--border);">
                        </div>

                        <label class="btn btn-ghost btn-sm"
                            style="cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                            <i data-lucide="upload" style="width:14px; height:14px;"></i>
                            <span>Select Image (JPG, PNG, WEBP max 5MB)</span>
                            <input type="file" name="image" id="fitness-image-input"
                                accept="image/jpeg,image/png,image/webp" style="display:none;"
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
                    <button type="button" class="btn btn-ghost"
                        onclick="document.getElementById('create-post-modal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="send" style="width:14px;height:14px;"></i>
                        Publish to Community
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== REPORT MODAL (Posts & Comments) ===== -->
    <div x-data="reportModal()"
        @open-report-modal.window="open($event.detail.targetId || $event.detail.postId, $event.detail.type || 'post')"
        x-cloak>
        <div x-show="isOpen" class="modal-overlay" @click.self="close()" style="display:flex;"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="modal-box" style="max-width:460px;" @keydown.escape.window="close()">
                <div class="modal-header">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div
                            style="width:34px; height:34px; border-radius:8px; background:rgba(245,158,11,0.15); display:flex; align-items:center; justify-content:center;">
                            <i data-lucide="flag" style="color:#f59e0b; width:18px; height:18px;"></i>
                        </div>
                        <span class="modal-title" x-text="type === 'comment' ? 'Report Comment' : 'Report Post'"></span>
                    </div>
                    <button type="button" class="btn-icon" @click="close()">
                        <i data-lucide="x" style="width:16px;height:16px;"></i>
                    </button>
                </div>

                <form @submit.prevent="submitReport()" style="padding:0;">
                    <div class="form-group">
                        <label class="form-label">Reason for reporting *</label>
                        <select x-model="reason" class="form-control" required>
                            <option value="">Select a reason...</option>
                            <option value="spam">Spam or advertising</option>
                            <option value="harassment">Harassment or bullying</option>
                            <option value="inappropriate">Inappropriate content</option>
                            <option value="misinformation">Misinformation</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Additional details (optional)</label>
                        <textarea x-model="details" class="form-control" rows="3" maxlength="1000"
                            placeholder="Provide additional context to help our review team..."></textarea>
                    </div>

                    <div style="padding:12px 0 0 0; display:flex; justify-content:space-between; align-items:center;">
                        <span x-show="feedbackMsg" x-text="feedbackMsg" style="font-size:12px;"
                            :style="feedbackOk ? 'color:#34d399' : 'color:#f87171'"></span>
                        <div style="display:flex; gap:8px; margin-left:auto;">
                            <button type="button" class="btn btn-ghost" @click="close()">Cancel</button>
                            <button type="submit" class="btn btn-primary" :disabled="submitting || !reason">
                                <i data-lucide="send" style="width:14px;height:14px;"></i>
                                <span x-text="submitting ? 'Sending...' : 'Submit Report'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Responsive Share Workout Button in Top Header */
            .btn-share-workout {
                padding: 8px 14px;
                font-size: 13px;
                gap: 6px;
            }
            .btn-share-text-short {
                display: none;
            }
            @media (max-width: 640px) {
                .btn-share-workout {
                    padding: 6px 10px;
                    font-size: 12px;
                    gap: 4px;
                }
            }
            @media (max-width: 430px) {
                .btn-share-workout {
                    padding: 6px 8px;
                    font-size: 11.5px;
                }
                .btn-share-text-full {
                    display: none;
                }
                .btn-share-text-short {
                    display: inline;
                }
            }
            @media (max-width: 340px) {
                .btn-share-workout {
                    padding: 6px 6px;
                    font-size: 11px;
                }
            }

            /* Container & Layout Constraints */
            .fitness-container,
            .fitness-metrics-grid,
            .fitness-filter-card,
            .fitness-feed-stream,
            .post-card,
            .post-interactions-wrap,
            .post-comments-thread,
            .comment-item-card,
            .reply-item-card {
                width: 100%;
                max-width: 100%;
                box-sizing: border-box;
            }

            .replies-thread-container {
                margin-left: 12px;
                padding-left: 10px;
                border-left: 2px solid rgba(124, 58, 237, 0.25);
                display: flex;
                flex-direction: column;
                gap: 8px;
                min-width: 0;
                width: 100%;
                box-sizing: border-box;
            }

            .post-dropdown-menu {
                position: absolute;
                right: 0;
                top: calc(100% + 6px);
                min-width: 180px;
                max-width: calc(100vw - 32px);
                background: var(--bg-card);
                border: 1px solid var(--border);
                border-radius: var(--radius-md);
                box-shadow: 0 8px 30px rgba(0,0,0,0.4);
                z-index: 100;
                padding: 4px 0;
                backdrop-filter: blur(20px);
            }

            .comment-dropdown-menu {
                position: absolute;
                right: 0;
                top: calc(100% + 4px);
                min-width: 150px;
                max-width: calc(100vw - 32px);
                background: var(--bg-card);
                border: 1px solid var(--border);
                border-radius: var(--radius-md);
                box-shadow: 0 8px 30px rgba(0,0,0,0.4);
                z-index: 110;
                padding: 4px 0;
                backdrop-filter: blur(20px);
            }

            .reaction-popover {
                position: absolute;
                bottom: calc(100% + 4px);
                left: 0;
                max-width: calc(100vw - 32px);
                background: var(--bg-card);
                border: 1px solid var(--border);
                border-radius: 24px;
                box-shadow: 0 8px 30px rgba(0,0,0,0.5);
                backdrop-filter: blur(24px);
                z-index: 120;
                display: flex;
                gap: 4px;
                padding: 3px 6px;
            }

            /* Post & Comment action dropdown menu items */
            .post-menu-item,
            .comment-menu-item {
                display: flex;
                align-items: center;
                gap: 9px;
                width: 100%;
                padding: 8px 13px;
                font-size: 12.5px;
                color: var(--text-secondary);
                background: none;
                border: none;
                cursor: pointer;
                text-decoration: none;
                text-align: left;
                transition: background 0.12s ease, color 0.12s ease;
                white-space: nowrap;
            }

            .post-menu-item:hover,
            .post-menu-item:focus-visible,
            .comment-menu-item:hover,
            .comment-menu-item:focus-visible {
                background: rgba(124, 58, 237, 0.08);
                color: var(--text-primary);
                outline: none;
            }

            .post-menu-item:active,
            .comment-menu-item:active {
                background: rgba(124, 58, 237, 0.14);
            }

            /* Hover states for header action buttons */
            .glass-card .btn-icon:hover {
                background: rgba(255, 255, 255, 0.08) !important;
                border-color: rgba(124, 58, 237, 0.3) !important;
            }

            .glass-card .btn-icon:focus-visible {
                outline: 2px solid var(--accent-purple);
                outline-offset: 2px;
            }

            /* Reaction & Reply Pills */
            .comment-action-pill {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 3px 8px;
                border-radius: 14px;
                background: rgba(255, 255, 255, 0.03);
                border: 1px solid var(--border);
                color: var(--text-muted);
                font-size: 11px;
                cursor: pointer;
                transition: all 0.15s ease;
            }

            .comment-action-pill:hover {
                background: rgba(124, 58, 237, 0.1);
                border-color: rgba(124, 58, 237, 0.3);
                color: var(--text-primary);
            }

            .comment-action-pill.active {
                background: rgba(124, 58, 237, 0.18);
                border-color: rgba(124, 58, 237, 0.45);
                color: #a78bfa;
            }

            .reaction-badge-pill {
                display: inline-flex;
                align-items: center;
                gap: 3px;
                padding: 2px 7px;
                border-radius: 12px;
                background: rgba(255, 255, 255, 0.04);
                border: 1px solid var(--border);
                font-size: 11px;
                color: var(--text-secondary);
                cursor: pointer;
                transition: all 0.15s ease;
            }

            .reaction-badge-pill:hover {
                background: rgba(124, 58, 237, 0.12);
                border-color: rgba(124, 58, 237, 0.35);
            }

            .reaction-badge-pill.user-reacted {
                background: rgba(124, 58, 237, 0.2);
                border-color: rgba(124, 58, 237, 0.5);
                color: #c4b5fd;
                font-weight: 600;
            }

            .reaction-emoji-btn {
                font-size: 17px;
                line-height: 1;
                padding: 4px 6px;
                background: none;
                border: none;
                border-radius: 50%;
                cursor: pointer;
                transition: transform 0.12s ease, background 0.12s ease;
            }

            .reaction-emoji-btn:hover {
                transform: scale(1.3);
                background: rgba(124, 58, 237, 0.2);
            }

            .reaction-emoji-btn.selected {
                background: rgba(124, 58, 237, 0.3);
                box-shadow: 0 0 8px rgba(124, 58, 237, 0.5);
            }

            @media (max-width: 480px) {
                .fitness-container {
                    gap: 14px !important;
                }
                .fitness-metrics-grid {
                    gap: 8px !important;
                }
                .fitness-filter-card {
                    padding: 10px 12px !important;
                }
                .post-header {
                    padding: 12px 12px 10px 12px !important;
                }
                .post-metrics-bar {
                    padding: 8px 12px !important;
                    gap: 6px 10px !important;
                }
                .post-caption {
                    padding: 12px 12px 10px 12px !important;
                    font-size: 13px !important;
                }
                .post-image-wrap {
                    padding: 0 12px 12px 12px !important;
                }
                .post-actions-bar {
                    padding: 8px 12px !important;
                }
                .post-comments-thread {
                    padding: 12px 10px !important;
                }
                .comment-item-card {
                    padding: 8px 8px !important;
                }
                .reply-item-card {
                    padding: 6px 6px !important;
                }
                .replies-thread-container {
                    margin-left: 6px !important;
                    padding-left: 6px !important;
                    gap: 6px !important;
                }
                .reaction-popover {
                    left: auto !important;
                    right: 0 !important;
                }
                .modal-metrics-grid {
                    grid-template-columns: 1fr !important;
                    gap: 10px !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            /**
             * Alpine.js component for per-post actions (save, hide, share).
             */
            function postActions(postId, initialSaved) {
                return {
                    postId: postId,
                    isSaved: initialSaved,

                    async toggleSave() {
                        try {
                            const res = await fetch(`/fitness/${this.postId}/save`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });
                            const data = await res.json();
                            this.isSaved = data.saved;
                            showToast(data.message, 'success');
                            if (typeof lucide !== 'undefined') lucide.createIcons();
                        } catch (e) {
                            showToast('Failed to save/unsave post.', 'error');
                        }
                    },

                    async hidePost() {
                        try {
                            const res = await fetch(`/fitness/${this.postId}/hide`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });
                            const data = await res.json();

                            const card = document.getElementById('post-' + this.postId);
                            if (card) {
                                card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                                card.style.opacity = '0';
                                card.style.transform = 'scale(0.97)';
                                setTimeout(() => {
                                    card.style.display = 'none';
                                }, 300);
                            }

                            showUndoToast('Post hidden from your feed.', async () => {
                                await fetch(`/fitness/${this.postId}/hide`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content,
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                    },
                                });
                                if (card) {
                                    card.style.display = '';
                                    card.style.opacity = '1';
                                    card.style.transform = 'none';
                                }
                                showToast('Post restored to your feed.', 'success');
                            });
                        } catch (e) {
                            showToast('Failed to hide post.', 'error');
                        }
                    },

                    async sharePost() {
                        const shareUrl = `${window.location.origin}/fitness#post-${this.postId}`;
                        const shareData = {
                            title: 'Fitness Community Post',
                            text: 'Check out this workout update!',
                            url: shareUrl,
                        };

                        try {
                            if (navigator.share && navigator.canShare && navigator.canShare(shareData)) {
                                await navigator.share(shareData);
                                return;
                            }
                        } catch (e) {
                            if (e.name === 'AbortError') return;
                        }

                        try {
                            await navigator.clipboard.writeText(shareUrl);
                            showToast('Link copied to clipboard!', 'success');
                        } catch (e) {
                            const tmp = document.createElement('input');
                            tmp.value = shareUrl;
                            document.body.appendChild(tmp);
                            tmp.select();
                            document.execCommand('copy');
                            document.body.removeChild(tmp);
                            showToast('Link copied to clipboard!', 'success');
                        }
                    },
                };
            }

            /**
             * Alpine.js component for per-comment & per-reply actions.
             */
            function commentActions(commentId, postId, initialComment, initialEdited, initialHidden, initialReaction,
                initialReactionCounts, initialTotalReactions, authorName) {
                return {
                    commentId: commentId,
                    postId: postId,
                    commentText: initialComment,
                    editText: initialComment,
                    authorName: authorName,
                    editing: false,
                    isEdited: initialEdited,
                    isHidden: initialHidden,
                    isSaving: false,
                    menuOpen: false,

                    // Reaction state
                    pickerOpen: false,
                    userReaction: initialReaction,
                    reactionCounts: initialReactionCounts || {},
                    totalReactions: initialTotalReactions || 0,

                    // Reply state
                    replying: false,
                    replyText: '',
                    replyingToName: authorName,
                    isSubmittingReply: false,
                    repliesOpen: true,

                    init() {
                        window.addEventListener('open-reply-composer', (e) => {
                            if (e.detail && e.detail.commentId === this.commentId) {
                                this.openReply(e.detail.mentionName);
                            }
                        });
                    },

                    reactionEmoji(type) {
                        const map = {
                            like: '👍',
                            love: '❤️',
                            celebrate: '🎉',
                            laugh: '😂',
                            fire: '🔥'
                        };
                        return map[type] || '';
                    },

                    capitalize(str) {
                        if (!str) return '';
                        return str.charAt(0).toUpperCase() + str.slice(1);
                    },

                    async toggleReaction(type) {
                        this.pickerOpen = false;
                        try {
                            const res = await fetch(`/fitness/comments/${this.commentId}/react`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify({
                                    reaction_type: type
                                })
                            });
                            const data = await res.json();
                            if (data.success) {
                                this.userReaction = data.user_reaction;
                                this.reactionCounts = data.reaction_counts || {};
                                this.totalReactions = data.total_reactions || 0;
                                showToast(data.message, 'success');
                            }
                        } catch (e) {
                            showToast('Failed to update reaction.', 'error');
                        }
                    },

                    openReply(mentionName = null) {
                        this.replying = true;
                        this.replyingToName = mentionName || this.authorName;
                        this.replyText = mentionName ? `@${mentionName} ` : '';
                        this.$nextTick(() => {
                            const form = this.$el.querySelector('textarea[name="comment"]');
                            if (form) {
                                form.focus();
                                form.selectionStart = form.selectionEnd = form.value.length;
                            }
                        });
                    },

                    cancelReply() {
                        this.replying = false;
                        this.replyText = '';
                    },

                    startEdit() {
                        this.editText = this.commentText;
                        this.editing = true;
                        this.menuOpen = false;
                    },

                    cancelEdit() {
                        this.editing = false;
                        this.editText = this.commentText;
                    },

                    async saveEdit() {
                        if (!this.editText.trim() || this.isSaving) return;
                        this.isSaving = true;

                        try {
                            const res = await fetch(`/fitness/comments/${this.commentId}`, {
                                method: 'PUT',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify({
                                    comment: this.editText
                                }),
                            });

                            if (!res.ok) {
                                const errData = await res.json().catch(() => ({}));
                                const msg = errData.message || (errData.errors && errData.errors.comment ? errData.errors
                                    .comment[0] : 'Failed to update comment.');
                                showToast(msg, 'error');
                                return;
                            }

                            const data = await res.json();
                            this.commentText = data.comment;
                            this.isEdited = true;
                            this.editing = false;
                            showToast(data.message || 'Comment updated successfully.', 'success');
                        } catch (e) {
                            showToast('Failed to update comment.', 'error');
                        } finally {
                            this.isSaving = false;
                        }
                    },

                    async deleteComment() {
                        if (!confirm('Are you sure you want to delete this?')) {
                            return;
                        }

                        try {
                            const res = await fetch(`/fitness/comments/${this.commentId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });

                            if (!res.ok) {
                                showToast('Failed to delete.', 'error');
                                return;
                            }

                            const data = await res.json();

                            const el = document.getElementById('comment-' + this.commentId);
                            if (el) {
                                el.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                                el.style.opacity = '0';
                                el.style.transform = 'scale(0.96)';
                                setTimeout(() => el.remove(), 250);
                            }

                            if (typeof data.comments_count !== 'undefined') {
                                const countEl = document.getElementById('comments-count-' + this.postId);
                                if (countEl) {
                                    countEl.textContent = data.comments_count + (data.comments_count === 1 ? ' Comment' :
                                        ' Comments');
                                }
                            }

                            showToast(data.message || 'Deleted successfully.', 'success');
                        } catch (e) {
                            showToast('Failed to delete.', 'error');
                        }
                    },

                    async hideComment() {
                        try {
                            const res = await fetch(`/fitness/comments/${this.commentId}/hide`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });

                            const data = await res.json();
                            this.isHidden = true;

                            const el = document.getElementById('comment-' + this.commentId);
                            if (el) {
                                el.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                                el.style.opacity = '0';
                                el.style.transform = 'scale(0.96)';
                                setTimeout(() => {
                                    el.style.display = 'none';
                                }, 250);
                            }

                            showUndoToast('Hidden from your view.', async () => {
                                await fetch(`/fitness/comments/${this.commentId}/hide`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content,
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                    },
                                });
                                this.isHidden = false;
                                if (el) {
                                    el.style.display = '';
                                    el.style.opacity = '1';
                                    el.style.transform = 'none';
                                }
                                showToast('Restored to your view.', 'success');
                            });
                        } catch (e) {
                            showToast('Failed to hide.', 'error');
                        }
                    },

                    async unhideComment() {
                        try {
                            const res = await fetch(`/fitness/comments/${this.commentId}/hide`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });

                            const data = await res.json();
                            this.isHidden = false;
                            showToast(data.message || 'Restored to your view.', 'success');
                        } catch (e) {
                            showToast('Failed to unhide.', 'error');
                        }
                    },

                    async copyComment() {
                        try {
                            if (navigator.clipboard && navigator.clipboard.writeText) {
                                await navigator.clipboard.writeText(this.commentText);
                                showToast('Copied to clipboard!', 'success');
                                return;
                            }
                        } catch (e) {
                            // Fallback
                        }

                        try {
                            const tmp = document.createElement('textarea');
                            tmp.value = this.commentText;
                            tmp.style.position = 'fixed';
                            tmp.style.opacity = '0';
                            document.body.appendChild(tmp);
                            tmp.focus();
                            tmp.select();
                            document.execCommand('copy');
                            document.body.removeChild(tmp);
                            showToast('Copied to clipboard!', 'success');
                        } catch (e) {
                            showToast('Failed to copy.', 'error');
                        }
                    },
                };
            }

            /**
             * Alpine.js component for the report modal.
             */
            function reportModal() {
                return {
                    isOpen: false,
                    targetId: null,
                    type: 'post',
                    reason: '',
                    details: '',
                    submitting: false,
                    feedbackMsg: '',
                    feedbackOk: false,

                    open(targetId, type = 'post') {
                        this.targetId = targetId;
                        this.type = type;
                        this.reason = '';
                        this.details = '';
                        this.feedbackMsg = '';
                        this.isOpen = true;
                        if (typeof lucide !== 'undefined') setTimeout(() => lucide.createIcons(), 50);
                    },

                    close() {
                        this.isOpen = false;
                        this.feedbackMsg = '';
                    },

                    async submitReport() {
                        if (!this.reason) return;
                        this.submitting = true;
                        this.feedbackMsg = '';

                        const url = this.type === 'comment' ?
                            `/fitness/comments/${this.targetId}/report` :
                            `/fitness/${this.targetId}/report`;

                        try {
                            const res = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify({
                                    reason: this.reason,
                                    details: this.details
                                }),
                            });

                            const data = await res.json();
                            this.feedbackOk = true;
                            this.feedbackMsg = data.message;

                            setTimeout(() => this.close(), 2000);
                        } catch (e) {
                            this.feedbackOk = false;
                            this.feedbackMsg = 'Failed to submit report. Please try again.';
                        } finally {
                            this.submitting = false;
                        }
                    },
                };
            }

            /**
             * Simple toast notification.
             */
            function showToast(message, type = 'success', duration = 3000) {
                const container = document.getElementById('undo-toast-container');
                if (!container) return;

                const toast = document.createElement('div');
                toast.style.cssText = `
            pointer-events:auto; padding:12px 20px; border-radius:10px; font-size:13px; font-weight:500;
            color:#fff; backdrop-filter:blur(12px); box-shadow:0 4px 20px rgba(0,0,0,0.3);
            display:flex; align-items:center; gap:8px; transition:all 0.3s ease; opacity:0; transform:translateY(10px);
            background:${type === 'error' ? 'rgba(239,68,68,0.9)' : 'rgba(30,30,40,0.92)'};
            border:1px solid ${type === 'error' ? 'rgba(239,68,68,0.4)' : 'rgba(124,58,237,0.3)'};
        `;
                toast.textContent = message;
                container.appendChild(toast);

                requestAnimationFrame(() => {
                    toast.style.opacity = '1';
                    toast.style.transform = 'translateY(0)';
                });

                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(10px)';
                    setTimeout(() => toast.remove(), 300);
                }, duration);
            }

            /**
             * Toast with Undo action button.
             */
            function showUndoToast(message, undoCallback, duration = 6000) {
                const container = document.getElementById('undo-toast-container');
                if (!container) return;

                const toast = document.createElement('div');
                toast.style.cssText = `
            pointer-events:auto; padding:12px 20px; border-radius:10px; font-size:13px; font-weight:500;
            color:#fff; backdrop-filter:blur(12px); box-shadow:0 4px 20px rgba(0,0,0,0.3);
            display:flex; align-items:center; gap:12px; transition:all 0.3s ease; opacity:0; transform:translateY(10px);
            background:rgba(30,30,40,0.92); border:1px solid rgba(124,58,237,0.3);
        `;

                const msgSpan = document.createElement('span');
                msgSpan.textContent = message;
                toast.appendChild(msgSpan);

                const undoBtn = document.createElement('button');
                undoBtn.textContent = 'Undo';
                undoBtn.style.cssText = `
            background:rgba(124,58,237,0.2); border:1px solid rgba(124,58,237,0.4); color:#a78bfa;
            padding:4px 12px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;
            transition:background 0.15s ease; white-space:nowrap;
        `;
                undoBtn.addEventListener('mouseenter', () => {
                    undoBtn.style.background = 'rgba(124,58,237,0.35)';
                });
                undoBtn.addEventListener('mouseleave', () => {
                    undoBtn.style.background = 'rgba(124,58,237,0.2)';
                });
                undoBtn.addEventListener('click', () => {
                    undoCallback();
                    toast.remove();
                });
                toast.appendChild(undoBtn);
                container.appendChild(toast);

                requestAnimationFrame(() => {
                    toast.style.opacity = '1';
                    toast.style.transform = 'translateY(0)';
                });

                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(10px)';
                    setTimeout(() => toast.remove(), 300);
                }, duration);
            }
        </script>
    @endpush
@endsection
