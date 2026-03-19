<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('redirects / to /all', function () {
    get('/')->assertRedirect('/all');
});

it('returns 200 for /all', function () {
    get('/all')->assertSuccessful();
});

it('returns 200 for /logbook', function () {
    get('/logbook')->assertSuccessful();
});

it('returns 200 for /trash', function () {
    get('/trash')->assertSuccessful();
});

it('shows logged items on /logbook', function () {
    $loggedItem = Item::factory()->create(['type' => 'To-Do', 'is_logged' => true, 'completion_date' => now()]);
    $notLoggedItem = Item::factory()->create(['type' => 'To-Do', 'is_logged' => false]);

    get('/logbook')
        ->assertSee($loggedItem->title)
        ->assertDontSee($notLoggedItem->title);
});

it('groups logbook items by completion date descending', function () {
    $older = Item::factory()->create(['type' => 'To-Do', 'is_logged' => true, 'completion_date' => now()->subDays(2)]);
    $newer = Item::factory()->create(['type' => 'To-Do', 'is_logged' => true, 'completion_date' => now()->subDay()]);

    $response = get('/logbook');
    $content = $response->getContent();

    $response->assertSee($newer->title)->assertSee($older->title);
    expect(strpos($content, $newer->title))->toBeLessThan(strpos($content, $older->title));
});

it('shows checkmark icon for completed items in logbook', function () {
    Item::factory()->create(['type' => 'To-Do', 'is_logged' => true, 'status' => 'Completed', 'completion_date' => now()]);

    get('/logbook')->assertSee('evenodd');
});

it('shows x-mark icon for canceled items in logbook', function () {
    Item::factory()->create(['type' => 'To-Do', 'is_logged' => true, 'status' => 'Canceled', 'completion_date' => now()]);

    get('/logbook')->assertSee('evenodd');
});

it('shows trashed items on /trash', function () {
    $trashedItem = Item::factory()->create(['type' => 'To-Do', 'is_trashed' => true]);
    $openItem = Item::factory()->create(['type' => 'To-Do', 'is_trashed' => false]);

    get('/trash')
        ->assertSee($trashedItem->title)
        ->assertDontSee($openItem->title);
});
