<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Get basic statistics to show in profile summary
        $stats = [
            'total_tasks'     => $user->tasks()->count(),
            'completed_tasks' => $user->tasks()->where('status', 'completed')->count(),
            'total_events'    => $user->events()->count(),
            'total_notes'     => $user->notes()->count(),
        ];

        return view('profile.edit', [
            'user'  => $user,
            'stats' => $stats,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Handle avatar upload if provided
        if ($request->hasFile('avatar_file')) {
            if ($user->avatar && !str_starts_with($user->avatar, 'http://') && !str_starts_with($user->avatar, 'https://') && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar_file')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        // Handle boolean preferences when submitting forms
        if ($request->has('_is_preferences')) {
            $validated['notify_email']  = $request->boolean('notify_email');
            $validated['notify_tasks']  = $request->boolean('notify_tasks');
            $validated['notify_events'] = $request->boolean('notify_events');
        }

        if ($request->has('_is_privacy')) {
            $validated['two_factor_enabled'] = $request->boolean('two_factor_enabled');
        }

        // Clean username formatting
        if (isset($validated['username'])) {
            $validated['username'] = ltrim(trim($validated['username']), '@');
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $redirectUrl = $request->filled('_tab')
            ? route('profile.edit', ['tab' => $request->input('_tab')])
            : route('profile.edit');

        return Redirect::to($redirectUrl)
            ->with('status', 'profile-updated')
            ->with('success', 'Profile settings updated successfully!');
    }

    /**
     * Upload and update user avatar directly (e.g. from the crop modal).
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar_file' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $user = $request->user();

        // Safely remove previous local avatar file if exists
        if ($user->avatar && !str_starts_with($user->avatar, 'http://') && !str_starts_with($user->avatar, 'https://') && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar_file')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return response()->json([
            'success'    => true,
            'avatar_url' => $user->avatar_url,
            'avatar'     => $path,
            'message'    => 'Profile photo updated successfully!',
        ]);
    }

    /**
     * Export all personal user data (tasks, events, notes, account info) as JSON.
     */
    public function export(Request $request): StreamedResponse
    {
        $user = $request->user();

        $exportData = [
            'export_date' => now()->toIso8601String(),
            'app'         => config('app.name', 'Smart Life Organizer'),
            'version'     => '1.2.0',
            'user_profile' => [
                'id'                 => $user->id,
                'name'               => $user->name,
                'username'           => $user->username_display,
                'email'              => $user->email,
                'phone'              => $user->phone,
                'birthday'           => $user->birthday?->format('Y-m-d'),
                'bio'                => $user->bio,
                'theme_preference'   => $user->theme_preference,
                'language'           => $user->language,
                'date_format'        => $user->date_format,
                'time_format'        => $user->time_format,
                'member_since'       => $user->created_at?->toIso8601String(),
            ],
            'tasks' => $user->tasks()->get()->map(function ($task) {
                return [
                    'id'           => $task->id,
                    'title'        => $task->title,
                    'description'  => $task->description,
                    'priority'     => $task->priority,
                    'status'       => $task->status,
                    'due_date'     => $task->due_date?->format('Y-m-d'),
                    'due_time'     => $task->due_time,
                    'completed_at' => $task->completed_at?->toIso8601String(),
                    'created_at'   => $task->created_at?->toIso8601String(),
                ];
            }),
            'events' => $user->events()->get()->map(function ($event) {
                return [
                    'id'             => $event->id,
                    'title'          => $event->title,
                    'description'    => $event->description,
                    'category'       => $event->category,
                    'color'          => $event->color,
                    'start_datetime' => $event->start_datetime?->toIso8601String(),
                    'end_datetime'   => $event->end_datetime?->toIso8601String(),
                    'is_all_day'     => (bool) $event->is_all_day,
                    'created_at'     => $event->created_at?->toIso8601String(),
                ];
            }),
            'notes' => $user->notes()->get()->map(function ($note) {
                return [
                    'id'         => $note->id,
                    'title'      => $note->title,
                    'content'    => $note->content,
                    'color'      => $note->color,
                    'is_pinned'  => (bool) $note->is_pinned,
                    'created_at' => $note->created_at?->toIso8601String(),
                    'updated_at' => $note->updated_at?->toIso8601String(),
                ];
            }),
        ];

        $fileName = 'smart_life_data_' . $user->username_display . '_' . now()->format('Ymd_His') . '.json';

        return response()->streamDownload(function () use ($exportData) {
            echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }, $fileName, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
