<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $view   = $request->get('view', 'month');

        $now = Carbon::now();

        // Month view
        $monthYear = $request->get('month', $now->format('Y-m'));
        $monthDate = Carbon::createFromFormat('Y-m', $monthYear)->startOfMonth();
        $monthStart = $monthDate->copy()->startOfWeek(Carbon::SUNDAY);
        $monthEnd   = $monthDate->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $monthEvents = Event::forUser($userId)
            ->whereBetween('start_datetime', [$monthStart, $monthEnd])
            ->orderBy('start_datetime')
            ->get()
            ->groupBy(fn($e) => $e->start_datetime->format('Y-m-d'));

        // Week view
        $weekStart = $request->get('week_start')
            ? Carbon::parse($request->get('week_start'))->startOfWeek(Carbon::SUNDAY)
            : $now->copy()->startOfWeek(Carbon::SUNDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SATURDAY);

        $weekEvents = Event::forUser($userId)
            ->whereBetween('start_datetime', [$weekStart, $weekEnd])
            ->orderBy('start_datetime')
            ->get()
            ->groupBy(fn($e) => $e->start_datetime->format('Y-m-d'));

        // Day view
        $dayDate  = $request->get('day') ? Carbon::parse($request->get('day')) : $now->copy();
        $dayEvents = Event::forUser($userId)
            ->whereDate('start_datetime', $dayDate)
            ->orderBy('start_datetime')
            ->get();

        return view('calendar.index', compact(
            'view', 'monthDate', 'monthStart', 'monthEnd', 'monthEvents',
            'weekStart', 'weekEnd', 'weekEvents',
            'dayDate', 'dayEvents'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'category'       => 'required|in:appointment,study,work,personal,other',
            'color'          => 'nullable|string|max:10',
            'start_datetime' => 'required|date',
            'end_datetime'   => 'nullable|date|after_or_equal:start_datetime',
            'is_all_day'     => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['color']   = $validated['color'] ?? '#7c3aed';
        Event::create($validated);

        return redirect()->route('calendar.index')->with('success', 'Event created!');
    }

    public function update(Request $request, Event $event)
    {
        abort_if($event->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'category'       => 'required|in:appointment,study,work,personal,other',
            'color'          => 'required|string|max:10',
            'start_datetime' => 'required|date',
            'end_datetime'   => 'nullable|date|after_or_equal:start_datetime',
            'is_all_day'     => 'boolean',
        ]);

        $event->update($validated);

        return redirect()->route('calendar.index')->with('success', 'Event updated!');
    }

    public function destroy(Event $event)
    {
        abort_if($event->user_id !== Auth::id(), 403);
        $event->delete();
        return back()->with('success', 'Event deleted!');
    }
}
