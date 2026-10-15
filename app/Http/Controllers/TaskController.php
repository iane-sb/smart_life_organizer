<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query  = Task::forUser($userId);

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $tasks = $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
                       ->orderBy('due_date')
                       ->get();

        $pending    = $tasks->where('status', 'pending');
        $inProgress = $tasks->where('status', 'in_progress');
        $completed  = $tasks->where('status', 'completed');

        $view = $request->get('view', 'kanban');

        return view('tasks.index', compact('tasks', 'pending', 'inProgress', 'completed', 'view'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority'    => 'required|in:low,medium,high,urgent',
            'status'      => 'required|in:pending,in_progress,completed',
            'due_date'    => 'nullable|date',
            'due_time'    => 'nullable|date_format:H:i',
        ]);

        $validated['user_id'] = Auth::id();
        if ($validated['status'] === 'completed') {
            $validated['completed_at'] = now();
        }

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully!');
    }

    public function update(Request $request, Task $task)
    {
        $this->authorizeTask($task);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority'    => 'required|in:low,medium,high,urgent',
            'status'      => 'required|in:pending,in_progress,completed',
            'due_date'    => 'nullable|date',
            'due_time'    => 'nullable|date_format:H:i',
        ]);

        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'completed') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully!');
    }

    public function toggleStatus(Task $task)
    {
        $this->authorizeTask($task);

        if ($task->status === 'completed') {
            $task->update(['status' => 'pending', 'completed_at' => null]);
        } else {
            $task->update(['status' => 'completed', 'completed_at' => now()]);
        }

        return back()->with('success', 'Task status updated!');
    }

    public function destroy(Task $task)
    {
        $this->authorizeTask($task);
        $task->delete();
        return back()->with('success', 'Task deleted!');
    }

    private function authorizeTask(Task $task)
    {
        abort_if($task->user_id !== Auth::id(), 403);
    }
}
