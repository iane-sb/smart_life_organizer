<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Note;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Demo user
        $user = User::firstOrCreate(
            ['email' => 'demo@smartlife.com'],
            [
                'name'     => 'Alex Johnson',
                'password' => Hash::make('password'),
            ]
        );

        // Tasks
        $tasks = [
            ['title' => 'Review quarterly report', 'priority' => 'urgent', 'status' => 'pending', 'due_date' => today()->addDay(), 'description' => 'Go through Q3 financials and prepare summary for the board meeting.'],
            ['title' => 'Team standup meeting', 'priority' => 'high', 'status' => 'in_progress', 'due_date' => today(), 'description' => 'Daily sync with the dev team.'],
            ['title' => 'Update project documentation', 'priority' => 'medium', 'status' => 'pending', 'due_date' => today()->addDays(3), 'description' => 'Rewrite the README and add API docs.'],
            ['title' => 'Buy groceries', 'priority' => 'low', 'status' => 'pending', 'due_date' => today(), 'description' => 'Milk, eggs, bread, coffee beans.'],
            ['title' => 'Complete Laravel project', 'priority' => 'high', 'status' => 'in_progress', 'due_date' => today()->addDays(5), 'description' => 'Build the Smart Life Organizer app.'],
            ['title' => 'Go for a run', 'priority' => 'medium', 'status' => 'completed', 'due_date' => today(), 'completed_at' => now()->subHours(2)],
            ['title' => 'Read 30 pages', 'priority' => 'low', 'status' => 'completed', 'due_date' => today(), 'completed_at' => now()->subHour()],
            ['title' => 'Fix login bug', 'priority' => 'urgent', 'status' => 'completed', 'due_date' => today()->subDay(), 'completed_at' => now()->subDays(1)],
            ['title' => 'Study algorithms chapter 5', 'priority' => 'medium', 'status' => 'pending', 'due_date' => today()->addDays(2)],
            ['title' => 'Call dentist for appointment', 'priority' => 'high', 'status' => 'pending', 'due_date' => today()->addDays(1)],
        ];

        foreach ($tasks as $t) {
            Task::create(array_merge($t, ['user_id' => $user->id]));
        }

        // Add some completed tasks from past days for streak
        for ($i = 1; $i <= 5; $i++) {
            Task::create([
                'user_id'      => $user->id,
                'title'        => "Daily goal day -$i",
                'priority'     => 'low',
                'status'       => 'completed',
                'due_date'     => today()->subDays($i),
                'completed_at' => Carbon::today()->subDays($i)->addHours(20),
            ]);
        }

        // Events
        $events = [
            ['title' => 'Doctor Appointment', 'category' => 'appointment', 'color' => '#f59e0b', 'start_datetime' => today()->addDays(2)->setHour(10), 'end_datetime' => today()->addDays(2)->setHour(11)],
            ['title' => 'Team Sprint Review', 'category' => 'work', 'color' => '#8b5cf6', 'start_datetime' => today()->addDay()->setHour(14), 'end_datetime' => today()->addDay()->setHour(15)->setMinute(30)],
            ['title' => 'Data Structures Exam', 'category' => 'study', 'color' => '#3b82f6', 'start_datetime' => today()->addDays(5)->setHour(9), 'end_datetime' => today()->addDays(5)->setHour(11)],
            ['title' => "Mom's Birthday", 'category' => 'personal', 'color' => '#ec4899', 'start_datetime' => today()->addDays(4)->setHour(0), 'is_all_day' => true],
            ['title' => 'Project Deadline', 'category' => 'work', 'color' => '#ef4444', 'start_datetime' => today()->addDays(7)->setHour(23)->setMinute(59)],
            ['title' => 'Morning Yoga', 'category' => 'personal', 'color' => '#06b6d4', 'start_datetime' => today()->addDay()->setHour(7), 'end_datetime' => today()->addDay()->setHour(8)],
            ['title' => 'Gym Session', 'category' => 'personal', 'color' => '#10b981', 'start_datetime' => today()->setHour(18), 'end_datetime' => today()->setHour(19)->setMinute(30)],
        ];

        foreach ($events as $e) {
            Event::create(array_merge($e, ['user_id' => $user->id]));
        }

        // Notes
        $notes = [
            ['title' => '🎯 2024 Goals', 'color' => '#7c3aed', 'is_pinned' => true, 'content' => "1. Learn a new programming language\n2. Exercise 4x per week\n3. Read 24 books\n4. Build 3 side projects\n5. Save 20% of income"],
            ['title' => 'Project Ideas 💡', 'color' => '#2563eb', 'is_pinned' => true, 'content' => "- AI-powered todo app\n- Recipe recommender\n- Budget tracker with charts\n- Habit streak app\n- Portfolio website v3"],
            ['title' => 'Shopping List', 'color' => '#10b981', 'content' => "□ Milk\n□ Eggs\n□ Coffee (dark roast)\n□ Sourdough bread\n□ Avocados\n□ Salmon"],
            ['title' => 'Book Recommendations', 'color' => '#f59e0b', 'content' => "📚 Must-reads:\n- Atomic Habits - James Clear\n- Deep Work - Cal Newport\n- The Pragmatic Programmer\n- Clean Code - Robert Martin\n- Thinking, Fast and Slow"],
            ['title' => 'Meeting Notes - Oct 14', 'color' => '#06b6d4', 'content' => "Key decisions:\n- Launch new feature by Nov 1\n- Hire 2 backend devs\n- Migrate to microservices by Q1\n- Budget approved for cloud infra"],
            ['title' => 'Workout Plan 💪', 'color' => '#ec4899', 'content' => "Mon: Chest + Triceps\nTue: Back + Biceps\nWed: Legs + Core\nThu: Shoulders\nFri: Full body HIIT\nSat/Sun: Rest or yoga"],
            ['title' => 'Coding Snippets', 'color' => '#64748b', 'content' => "Quick Git commands:\ngit stash pop\ngit rebase -i HEAD~3\ngit cherry-pick abc123\ngit log --oneline --graph"],
        ];

        foreach ($notes as $n) {
            Note::create(array_merge($n, ['user_id' => $user->id]));
        }
    }
}
