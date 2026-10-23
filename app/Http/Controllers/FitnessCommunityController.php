<?php

namespace App\Http\Controllers;

use App\Models\FitnessCommentHide;
use App\Models\FitnessCommentReaction;
use App\Models\FitnessCommentReport;
use App\Models\FitnessPost;
use App\Models\FitnessPostComment;
use App\Models\FitnessPostHide;
use App\Models\FitnessPostLike;
use App\Models\FitnessPostReport;
use App\Models\FitnessPostSave;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FitnessCommunityController extends Controller
{
    /**
     * Display the fitness community feed.
     * Hidden posts are excluded unless ?show_hidden=1 is present.
     * Hidden comments are excluded from comments unless ?show_hidden_comments=1 is present.
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();
        $showHidden = $request->boolean('show_hidden');
        $showHiddenComments = $request->boolean('show_hidden_comments');

        $query = FitnessPost::withCount('comments')->with([
            'user',
            'likes',
            'comments' => function ($q) use ($userId, $showHiddenComments) {
                $q->whereNull('parent_id');
                if (! $showHiddenComments) {
                    $q->whereDoesntHave('hides', function ($hq) use ($userId) {
                        $hq->where('user_id', $userId);
                    });
                }
                $q->with([
                    'user',
                    'hides',
                    'reactions',
                    'replies' => function ($rq) use ($userId, $showHiddenComments) {
                        if (! $showHiddenComments) {
                            $rq->whereDoesntHave('hides', function ($hq) use ($userId) {
                                $hq->where('user_id', $userId);
                            });
                        }
                        $rq->with(['user', 'hides', 'reactions'])->oldest();
                    },
                ])->oldest();
            },
            'saves',
            'hides',
        ])->latest();

        // When show_hidden is active, show ONLY the user's hidden posts
        if ($showHidden) {
            $hiddenPostIds = FitnessPostHide::where('user_id', $userId)->pluck('fitness_post_id');
            $query->whereIn('id', $hiddenPostIds);
        } else {
            // Normal feed: exclude posts hidden by the current user
            $hiddenPostIds = FitnessPostHide::where('user_id', $userId)->pluck('fitness_post_id');
            if ($hiddenPostIds->isNotEmpty()) {
                $query->whereNotIn('id', $hiddenPostIds);
            }
        }

        $selectedType = $request->get('type');
        if ($selectedType && $selectedType !== 'all') {
            $query->where('workout_type', $selectedType);
        }

        $search = $request->get('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('caption', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('username', 'like', "%{$search}%");
                  });
            });
        }

        $posts = $query->paginate(15)->withQueryString();

        $workoutTypes = [
            'all'           => 'All Workouts',
            'general'       => 'General Fitness',
            'running'       => 'Running',
            'weightlifting' => 'Weightlifting',
            'cycling'       => 'Cycling',
            'hiit'          => 'HIIT / Cardio',
            'yoga'          => 'Yoga & Stretch',
            'walking'       => 'Walking',
            'swimming'      => 'Swimming',
            'other'         => 'Other',
        ];

        // Summary community metrics
        $totalCommunityPosts = FitnessPost::count();
        $userPostCount       = FitnessPost::where('user_id', $userId)->count();
        $userLikesReceived   = FitnessPostLike::whereHas('post', fn($q) => $q->where('user_id', $userId))->count();
        $hiddenCount         = FitnessPostHide::where('user_id', $userId)->count();
        $hiddenCommentsCount = FitnessCommentHide::where('user_id', $userId)->count();

        return view('fitness.index', compact(
            'posts',
            'selectedType',
            'search',
            'workoutTypes',
            'totalCommunityPosts',
            'userPostCount',
            'userLikesReceived',
            'showHidden',
            'hiddenCount',
            'showHiddenComments',
            'hiddenCommentsCount'
        ));
    }

    /**
     * Store a newly created fitness journey post.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'caption'          => ['required', 'string', 'max:2000'],
            'workout_type'     => ['required', 'string', 'in:general,running,weightlifting,cycling,hiit,yoga,walking,swimming,other'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'calories_burned'  => ['nullable', 'integer', 'min:1', 'max:20000'],
            'image'            => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('fitness_posts', 'public');
            $validated['image_path'] = $path;
        }

        $validated['user_id'] = Auth::id();

        FitnessPost::create($validated);

        return redirect()->route('fitness.index')->with('success', 'Your fitness update has been shared with the community!');
    }

    /**
     * Show edit form for a fitness post (author only).
     */
    public function edit(FitnessPost $post): View
    {
        abort_if($post->user_id !== Auth::id(), 403, 'Unauthorized action.');

        $workoutTypes = [
            'general'       => 'General Fitness',
            'running'       => 'Running',
            'weightlifting' => 'Weightlifting / Gym',
            'cycling'       => 'Cycling',
            'hiit'          => 'HIIT / High Intensity',
            'yoga'          => 'Yoga & Stretching',
            'walking'       => 'Walking / Hiking',
            'swimming'      => 'Swimming',
            'other'         => 'Other Sports',
        ];

        return view('fitness.edit', compact('post', 'workoutTypes'));
    }

    /**
     * Update an existing fitness post (author only).
     */
    public function update(Request $request, FitnessPost $post): RedirectResponse
    {
        abort_if($post->user_id !== Auth::id(), 403, 'Unauthorized action.');

        $validated = $request->validate([
            'caption'          => ['required', 'string', 'max:2000'],
            'workout_type'     => ['required', 'string', 'in:general,running,weightlifting,cycling,hiit,yoga,walking,swimming,other'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'calories_burned'  => ['nullable', 'integer', 'min:1', 'max:20000'],
            'image'            => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            if ($post->image_path && Storage::disk('public')->exists($post->image_path)) {
                Storage::disk('public')->delete($post->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('fitness_posts', 'public');
        }

        $post->update($validated);

        return redirect()->route('fitness.index')->with('success', 'Post updated successfully!');
    }

    /**
     * Toggle like on a fitness post.
     */
    public function toggleLike(Request $request, FitnessPost $post): JsonResponse|RedirectResponse
    {
        $userId = Auth::id();
        $existing = FitnessPostLike::where('fitness_post_id', $post->id)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            FitnessPostLike::create([
                'fitness_post_id' => $post->id,
                'user_id'         => $userId,
            ]);
            $liked = true;
        }

        $likesCount = $post->likes()->count();

        if ($request->wantsJson()) {
            return response()->json([
                'liked'       => $liked,
                'likes_count' => $likesCount,
            ]);
        }

        return back()->with('success', $liked ? 'Post liked!' : 'Post unliked!');
    }

    /**
     * Store a comment (or reply) on a fitness post.
     */
    public function storeComment(Request $request, FitnessPost $post): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'comment'   => ['required', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'integer', 'exists:fitness_post_comments,id'],
        ]);

        $parentId = null;
        if (! empty($validated['parent_id'])) {
            $targetComment = FitnessPostComment::where('fitness_post_id', $post->id)
                ->where('id', $validated['parent_id'])
                ->first();

            abort_if(! $targetComment, 422, 'Parent comment does not belong to this post.');
            abort_if($targetComment->isHiddenBy(Auth::user()), 422, 'Cannot reply to a hidden comment.');

            // Enforce single-level nesting
            $parentId = $targetComment->parent_id ?: $targetComment->id;
        }

        $comment = FitnessPostComment::create([
            'fitness_post_id' => $post->id,
            'parent_id'       => $parentId,
            'user_id'         => Auth::id(),
            'comment'         => $validated['comment'],
        ]);

        $comment->load(['user', 'reactions']);

        if ($request->wantsJson()) {
            return response()->json([
                'success'        => true,
                'comment'        => $comment,
                'parent_id'      => $parentId,
                'message'        => $parentId ? 'Reply posted!' : 'Comment posted!',
                'comments_count' => FitnessPostComment::where('fitness_post_id', $post->id)->count(),
            ]);
        }

        return back()->with('success', $parentId ? 'Your reply was posted!' : 'Your comment was posted!');
    }

    /**
     * Reply directly to a comment.
     */
    public function replyComment(Request $request, FitnessPostComment $comment): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $userId = Auth::id();
        abort_if($comment->isHiddenBy(Auth::user()), 422, 'Cannot reply to a hidden comment.');

        // Enforce 1-level reply nesting: if replying to a reply, target the root comment
        $effectiveParentId = $comment->parent_id ?: $comment->id;
        $post = $comment->post;

        $reply = FitnessPostComment::create([
            'fitness_post_id' => $post->id,
            'parent_id'       => $effectiveParentId,
            'user_id'         => $userId,
            'comment'         => $validated['comment'],
        ]);

        $reply->load(['user', 'reactions']);

        if ($request->wantsJson()) {
            return response()->json([
                'success'        => true,
                'reply'          => $reply,
                'parent_id'      => $effectiveParentId,
                'message'        => 'Reply posted!',
                'comments_count' => FitnessPostComment::where('fitness_post_id', $post->id)->count(),
            ]);
        }

        return back()->with('success', 'Your reply was posted!');
    }

    /**
     * Update an existing comment or reply (author only).
     */
    public function updateComment(Request $request, FitnessPostComment $comment): JsonResponse|RedirectResponse
    {
        abort_if($comment->user_id !== Auth::id(), 403, 'Unauthorized action.');

        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $comment->update([
            'comment' => $validated['comment'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success'   => true,
                'comment'   => $comment->comment,
                'is_edited' => $comment->isEdited(),
                'message'   => 'Comment updated successfully.',
            ]);
        }

        return back()->with('success', 'Comment updated successfully.');
    }

    /**
     * Delete a fitness post and its uploaded photo.
     */
    public function destroy(FitnessPost $post): RedirectResponse
    {
        abort_if($post->user_id !== Auth::id(), 403, 'Unauthorized action.');

        if ($post->image_path && Storage::disk('public')->exists($post->image_path)) {
            Storage::disk('public')->delete($post->image_path);
        }

        $post->delete();

        return redirect()->route('fitness.index')->with('success', 'Fitness post deleted.');
    }

    /**
     * Delete a comment or reply (allowed for comment author or parent post owner).
     */
    public function destroyComment(Request $request, FitnessPostComment $comment): JsonResponse|RedirectResponse
    {
        $userId = Auth::id();
        abort_if($comment->user_id !== $userId && $comment->post->user_id !== $userId, 403, 'Unauthorized action.');

        $postId = $comment->fitness_post_id;
        $comment->delete();

        if ($request->wantsJson()) {
            $remainingCount = FitnessPostComment::where('fitness_post_id', $postId)->count();
            return response()->json([
                'success'        => true,
                'message'        => 'Comment deleted.',
                'comments_count' => $remainingCount,
            ]);
        }

        return back()->with('success', 'Comment deleted.');
    }

    /**
     * Hide a comment or reply from the current user's view.
     */
    public function hideComment(Request $request, FitnessPostComment $comment): JsonResponse|RedirectResponse
    {
        $userId = Auth::id();

        FitnessCommentHide::firstOrCreate([
            'fitness_post_comment_id' => $comment->id,
            'user_id'                 => $userId,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'hidden'   => true,
                'message'  => 'Comment hidden from your view.',
                'undo_url' => route('fitness.comment.unhide', $comment),
            ]);
        }

        return back()->with('success', 'Comment hidden from your view.');
    }

    /**
     * Unhide a comment or reply (restore it to the current user's view).
     */
    public function unhideComment(Request $request, FitnessPostComment $comment): JsonResponse|RedirectResponse
    {
        $userId = Auth::id();

        FitnessCommentHide::where('fitness_post_comment_id', $comment->id)
            ->where('user_id', $userId)
            ->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'hidden'  => false,
                'message' => 'Comment restored to your view.',
            ]);
        }

        return back()->with('success', 'Comment restored to your view.');
    }

    /**
     * Report a comment or reply (database-backed workflow with duplicate prevention).
     */
    public function reportComment(Request $request, FitnessPostComment $comment): JsonResponse|RedirectResponse
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'reason'  => ['required', 'string', 'in:spam,harassment,inappropriate,misinformation,other'],
            'details' => ['nullable', 'string', 'max:1000'],
        ]);

        $existing = FitnessCommentReport::where('fitness_post_comment_id', $comment->id)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            $message = 'You have already reported this comment. Our team will review it.';
            if ($request->wantsJson()) {
                return response()->json(['reported' => true, 'duplicate' => true, 'message' => $message]);
            }
            return back()->with('info', $message);
        }

        FitnessCommentReport::create([
            'fitness_post_comment_id' => $comment->id,
            'user_id'                 => $userId,
            'reason'                  => $validated['reason'],
            'details'                 => $validated['details'] ?? null,
            'status'                  => 'pending',
        ]);

        $message = 'Comment reported. Our team will review it. Thank you for helping keep the community safe.';

        if ($request->wantsJson()) {
            return response()->json(['reported' => true, 'duplicate' => false, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    /**
     * Copy comment endpoint - safely returns only the comment text.
     */
    public function copyComment(FitnessPostComment $comment): JsonResponse
    {
        return response()->json([
            'comment' => $comment->comment,
        ]);
    }

    /**
     * Toggle reaction on a comment or reply (like, love, celebrate, laugh, fire).
     */
    public function toggleCommentReaction(Request $request, FitnessPostComment $comment): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'reaction_type' => ['required', 'string', 'in:' . implode(',', FitnessCommentReaction::ALLOWED_TYPES)],
        ]);

        $userId = Auth::id();
        $reactionType = $validated['reaction_type'];
        $userReaction = null;

        // Atomic transaction to prevent race conditions & duplicate reactions
        DB::transaction(function () use ($comment, $userId, $reactionType, &$userReaction) {
            $existing = FitnessCommentReaction::where('fitness_post_comment_id', $comment->id)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                if ($existing->reaction_type === $reactionType) {
                    // Clicking the same reaction removes it
                    $existing->delete();
                    $userReaction = null;
                } else {
                    // Changing to a different reaction
                    $existing->update(['reaction_type' => $reactionType]);
                    $userReaction = $reactionType;
                }
            } else {
                FitnessCommentReaction::create([
                    'fitness_post_comment_id' => $comment->id,
                    'user_id'                 => $userId,
                    'reaction_type'           => $reactionType,
                ]);
                $userReaction = $reactionType;
            }
        });

        $comment->load('reactions');
        $reactionCounts = $comment->reactionCounts();
        $totalReactions = $comment->totalReactions();

        if ($request->wantsJson()) {
            return response()->json([
                'success'         => true,
                'user_reaction'   => $userReaction,
                'reaction_counts' => $reactionCounts,
                'total_reactions' => $totalReactions,
                'message'         => $userReaction ? 'Reaction updated!' : 'Reaction removed.',
            ]);
        }

        return back()->with('success', $userReaction ? 'Reaction updated!' : 'Reaction removed.');
    }

    /**
     * Toggle save/unsave on a fitness post for the current user.
     */
    public function toggleSave(Request $request, FitnessPost $post): JsonResponse|RedirectResponse
    {
        $userId = Auth::id();
        $existing = FitnessPostSave::where('fitness_post_id', $post->id)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            $existing->delete();
            $saved = false;
            $message = 'Post unsaved.';
        } else {
            FitnessPostSave::firstOrCreate([
                'fitness_post_id' => $post->id,
                'user_id'         => $userId,
            ]);
            $saved = true;
            $message = 'Post saved!';
        }

        if ($request->wantsJson()) {
            return response()->json(['saved' => $saved, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    /**
     * Hide a fitness post from the current user's feed.
     */
    public function hide(Request $request, FitnessPost $post): JsonResponse|RedirectResponse
    {
        $userId = Auth::id();

        FitnessPostHide::firstOrCreate([
            'fitness_post_id' => $post->id,
            'user_id'         => $userId,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'hidden'   => true,
                'message'  => 'Post hidden from your feed.',
                'undo_url' => route('fitness.unhide', $post),
            ]);
        }

        return back()->with('success', 'Post hidden from your feed. You can view hidden posts from the filter bar.');
    }

    /**
     * Unhide a fitness post (restore it to the current user's feed).
     */
    public function unhide(Request $request, FitnessPost $post): JsonResponse|RedirectResponse
    {
        $userId = Auth::id();

        FitnessPostHide::where('fitness_post_id', $post->id)
            ->where('user_id', $userId)
            ->delete();

        if ($request->wantsJson()) {
            return response()->json(['hidden' => false, 'message' => 'Post restored to your feed.']);
        }

        return back()->with('success', 'Post restored to your feed.');
    }

    /**
     * Report a fitness post (minimal workflow with duplicate prevention).
     */
    public function report(Request $request, FitnessPost $post): JsonResponse|RedirectResponse
    {
        $userId = Auth::id();

        abort_if($post->user_id === $userId, 403, 'You cannot report your own post.');

        $validated = $request->validate([
            'reason'  => ['required', 'string', 'in:spam,harassment,inappropriate,misinformation,other'],
            'details' => ['nullable', 'string', 'max:1000'],
        ]);

        $existing = FitnessPostReport::where('fitness_post_id', $post->id)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            $message = 'You have already reported this post. Our team will review it.';
            if ($request->wantsJson()) {
                return response()->json(['reported' => true, 'duplicate' => true, 'message' => $message]);
            }
            return back()->with('info', $message);
        }

        FitnessPostReport::create([
            'fitness_post_id' => $post->id,
            'user_id'         => $userId,
            'reason'          => $validated['reason'],
            'details'         => $validated['details'] ?? null,
            'status'          => 'pending',
        ]);

        $message = 'Post reported. Our team will review it. Thank you for helping keep the community safe.';

        if ($request->wantsJson()) {
            return response()->json(['reported' => true, 'duplicate' => false, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    /**
     * Share endpoint - returns share data for the post.
     */
    public function share(FitnessPost $post): JsonResponse
    {
        return response()->json([
            'title' => 'Fitness Journey by ' . $post->user->name,
            'text'  => \Illuminate\Support\Str::limit($post->caption, 120),
            'url'   => route('fitness.index') . '#post-' . $post->id,
        ]);
    }
}
