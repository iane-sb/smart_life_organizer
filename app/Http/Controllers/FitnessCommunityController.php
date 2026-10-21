<?php

namespace App\Http\Controllers;

use App\Models\FitnessPost;
use App\Models\FitnessPostComment;
use App\Models\FitnessPostLike;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FitnessCommunityController extends Controller
{
    /**
     * Display the fitness community feed.
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();
        $query  = FitnessPost::with(['user', 'likes', 'comments.user'])->latest();

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

        return view('fitness.index', compact(
            'posts',
            'selectedType',
            'search',
            'workoutTypes',
            'totalCommunityPosts',
            'userPostCount',
            'userLikesReceived'
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
     * Store a comment on a fitness post.
     */
    public function storeComment(Request $request, FitnessPost $post): RedirectResponse
    {
        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        FitnessPostComment::create([
            'fitness_post_id' => $post->id,
            'user_id'         => Auth::id(),
            'comment'         => $validated['comment'],
        ]);

        return back()->with('success', 'Your comment was posted!');
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
     * Delete a comment (allowed for comment author or post owner).
     */
    public function destroyComment(FitnessPostComment $comment): RedirectResponse
    {
        $userId = Auth::id();
        abort_if($comment->user_id !== $userId && $comment->post->user_id !== $userId, 403, 'Unauthorized action.');

        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }
}
