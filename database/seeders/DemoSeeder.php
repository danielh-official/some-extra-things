<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\SmartList;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed demo data for screenshots — Items, Tags, and SmartLists with fake data.
     */
    public function run(): void
    {
        // Tags
        $work = Tag::factory()->create(['name' => 'Work']);
        $personal = Tag::factory()->create(['name' => 'Personal']);
        $urgent = Tag::factory()->create(['name' => 'Urgent']);
        $waiting = Tag::factory()->create(['name' => 'Waiting']);
        $someday = Tag::factory()->create(['name' => 'Someday']);
        $errands = Tag::factory()->create(['name' => 'Errands']);
        $home = Tag::factory()->create(['name' => 'Home']);

        // Projects
        $websiteProject = Item::factory()->create([
            'id' => (string) Str::uuid(),
            'type' => 'Project',
            'title' => 'Redesign Company Website',
            'status' => 'Open',
            'start' => 'Anytime',
            'start_date' => now()->subDays(3)->toDateString(),
            'tags' => [$work->name],
            'all_matching_tags' => [$work->name],
        ]);
        $websiteProject->tags()->attach([$work->id]);

        $homeRenovation = Item::factory()->create([
            'id' => (string) Str::uuid(),
            'type' => 'Project',
            'title' => 'Kitchen Renovation',
            'status' => 'Open',
            'start' => 'Anytime',
            'tags' => [$home->name, $personal->name],
            'all_matching_tags' => [$home->name, $personal->name],
        ]);
        $homeRenovation->tags()->attach([$home->id, $personal->id]);

        $learningProject = Item::factory()->create([
            'id' => (string) Str::uuid(),
            'type' => 'Project',
            'title' => 'Learn Spanish',
            'status' => 'Open',
            'start' => 'Someday',
            'tags' => [$personal->name, $someday->name],
            'all_matching_tags' => [$personal->name, $someday->name],
        ]);
        $learningProject->tags()->attach([$personal->id, $someday->id]);

        // To-Dos under the website project
        $websiteTodos = [
            ['title' => 'Write new copy for the homepage', 'tags' => [$work->name, $urgent->name], 'tagIds' => [$work->id, $urgent->id]],
            ['title' => 'Design hero section mockups', 'tags' => [$work->name], 'tagIds' => [$work->id]],
            ['title' => 'Review competitor sites', 'tags' => [$work->name], 'tagIds' => [$work->id]],
            ['title' => 'Get sign-off from stakeholders', 'tags' => [$work->name, $waiting->name], 'tagIds' => [$work->id, $waiting->id]],
            ['title' => 'Set up staging environment', 'tags' => [$work->name], 'tagIds' => [$work->id]],
        ];

        foreach ($websiteTodos as $todo) {
            $item = Item::factory()->create([
                'id' => (string) Str::uuid(),
                'type' => 'To-Do',
                'title' => $todo['title'],
                'parent_id' => $websiteProject->id,
                'status' => 'Open',
                'start' => 'Anytime',
                'start_date' => now()->toDateString(),
                'tags' => $todo['tags'],
                'all_matching_tags' => $todo['tags'],
            ]);
            $item->tags()->attach($todo['tagIds']);
        }

        // To-Dos under the home renovation project
        $homeTodos = [
            ['title' => 'Get quotes from contractors', 'tags' => [$home->name, $errands->name], 'tagIds' => [$home->id, $errands->id]],
            ['title' => 'Choose cabinet style', 'tags' => [$home->name], 'tagIds' => [$home->id]],
            ['title' => 'Order new appliances', 'tags' => [$home->name, $errands->name], 'tagIds' => [$home->id, $errands->id]],
            ['title' => 'Schedule plumber visit', 'tags' => [$home->name, $waiting->name], 'tagIds' => [$home->id, $waiting->id]],
        ];

        foreach ($homeTodos as $todo) {
            $item = Item::factory()->create([
                'id' => (string) Str::uuid(),
                'type' => 'To-Do',
                'title' => $todo['title'],
                'parent_id' => $homeRenovation->id,
                'status' => 'Open',
                'start' => 'Anytime',
                'tags' => $todo['tags'],
                'all_matching_tags' => $todo['tags'],
            ]);
            $item->tags()->attach($todo['tagIds']);
        }

        // Standalone inbox To-Dos
        $inboxTodos = [
            'Pick up dry cleaning',
            'Call dentist for appointment',
            'Renew car insurance',
            'Send thank-you email to client',
        ];

        foreach ($inboxTodos as $title) {
            Item::factory()->create([
                'id' => (string) Str::uuid(),
                'type' => 'To-Do',
                'title' => $title,
                'is_inbox' => true,
                'status' => 'Open',
                'tags' => [],
                'all_matching_tags' => [],
            ]);
        }

        // Today To-Dos (work)
        $todayWorkTodos = [
            ['title' => 'Prepare slides for Monday standup', 'tags' => [$work->name, $urgent->name], 'tagIds' => [$work->id, $urgent->id]],
            ['title' => 'Reply to Sarah about Q2 budget', 'tags' => [$work->name], 'tagIds' => [$work->id]],
            ['title' => 'Review pull requests', 'tags' => [$work->name], 'tagIds' => [$work->id]],
        ];

        foreach ($todayWorkTodos as $todo) {
            $item = Item::factory()->create([
                'id' => (string) Str::uuid(),
                'type' => 'To-Do',
                'title' => $todo['title'],
                'status' => 'Open',
                'start' => 'Anytime',
                'start_date' => now()->toDateString(),
                'tags' => $todo['tags'],
                'all_matching_tags' => $todo['tags'],
            ]);
            $item->tags()->attach($todo['tagIds']);
        }

        // Errands
        $errandTodos = [
            'Buy groceries for the week',
            'Return library books',
            'Drop off donation box',
        ];

        foreach ($errandTodos as $title) {
            $item = Item::factory()->create([
                'id' => (string) Str::uuid(),
                'type' => 'To-Do',
                'title' => $title,
                'status' => 'Open',
                'start' => 'Anytime',
                'tags' => [$errands->name, $personal->name],
                'all_matching_tags' => [$errands->name, $personal->name],
            ]);
            $item->tags()->attach([$errands->id, $personal->id]);
        }

        // A completed item
        $completed = Item::factory()->create([
            'id' => (string) Str::uuid(),
            'type' => 'To-Do',
            'title' => 'Set up project management tool',
            'status' => 'Completed',
            'is_logged' => true,
            'completion_date' => now()->subDay(),
            'tags' => [$work->name],
            'all_matching_tags' => [$work->name],
        ]);
        $completed->tags()->attach([$work->id]);

        // SmartLists
        SmartList::factory()->create([
            'name' => 'Work Tasks',
            'is_pinned_to_sidebar' => true,
            'criteria' => [
                'type' => 'tag',
                'tag' => 'Work',
                'operator' => 'equals',
            ],
        ]);

        SmartList::factory()->create([
            'name' => 'Urgent',
            'is_pinned_to_sidebar' => true,
            'criteria' => [
                'type' => 'tag',
                'tag' => 'Urgent',
                'operator' => 'equals',
            ],
        ]);

        SmartList::factory()->create([
            'name' => 'Waiting For',
            'is_pinned_to_sidebar' => false,
            'criteria' => [
                'type' => 'tag',
                'tag' => 'Waiting',
                'operator' => 'equals',
            ],
        ]);

        SmartList::factory()->create([
            'name' => 'Errands',
            'is_pinned_to_sidebar' => true,
            'criteria' => [
                'type' => 'tag',
                'tag' => 'Errands',
                'operator' => 'equals',
            ],
        ]);

        SmartList::factory()->create([
            'name' => 'Personal',
            'is_pinned_to_sidebar' => false,
            'criteria' => [
                'type' => 'tag',
                'tag' => 'Personal',
                'operator' => 'equals',
            ],
        ]);
    }
}
