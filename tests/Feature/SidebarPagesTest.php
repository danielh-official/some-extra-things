<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('redirects / to /inbox', function () {
    get('/')->assertRedirect('/inbox');
});

it('returns 200 for /inbox', function () {
    get('/inbox')->assertSuccessful();
});

it('returns 200 for /today', function () {
    get('/today')->assertSuccessful();
});

it('returns 200 for /upcoming', function () {
    get('/upcoming')->assertSuccessful();
});

it('returns 200 for /anytime', function () {
    get('/anytime')->assertSuccessful();
});

it('returns 200 for /someday', function () {
    get('/someday')->assertSuccessful();
});

it('returns 200 for /logbook', function () {
    get('/logbook')->assertSuccessful();
});

it('returns 200 for /trash', function () {
    get('/trash')->assertSuccessful();
});

it('shows inbox items on /inbox', function () {
    $inboxItem = Item::factory()->create(['is_inbox' => true, 'status' => 'Open']);
    $nonInboxItem = Item::factory()->create(['is_inbox' => false, 'status' => 'Open']);

    get('/inbox')
        ->assertSee($inboxItem->title)
        ->assertDontSee($nonInboxItem->title);
});

it('shows today items on /today', function () {
    $todayItem = Item::factory()->create(['status' => 'Open', 'start_date' => today()]);
    $futureItem = Item::factory()->create(['status' => 'Open', 'start_date' => today()->addDay()]);
    $noDateItem = Item::factory()->create(['status' => 'Open', 'start_date' => null]);

    get('/today')
        ->assertSee($todayItem->title)
        ->assertDontSee($futureItem->title)
        ->assertDontSee($noDateItem->title);
});

it('separates evening items under This Evening header on /today', function () {
    $regularItem = Item::factory()->create(['status' => 'Open', 'start_date' => today(), 'evening' => false]);
    $eveningItem = Item::factory()->create(['status' => 'Open', 'start_date' => today(), 'evening' => true]);

    $response = get('/today');

    $response->assertSee($regularItem->title)
        ->assertSee($eveningItem->title)
        ->assertSee('This Evening');

    $content = $response->getContent();
    expect(strpos($content, $regularItem->title))->toBeLessThan(strpos($content, 'This Evening'));
    expect(strpos($content, $eveningItem->title))->toBeGreaterThan(strpos($content, 'This Evening'));
});

it('shows upcoming items on /upcoming', function () {
    $futureItem = Item::factory()->create(['status' => 'Open', 'start_date' => today()->addDay()]);
    $todayItem = Item::factory()->create(['status' => 'Open', 'start_date' => today()]);

    get('/upcoming')
        ->assertSee($futureItem->title)
        ->assertDontSee($todayItem->title);
});

it('groups upcoming items by start date', function () {
    $day1 = Item::factory()->create(['status' => 'Open', 'start_date' => today()->addDays(1)]);
    $day2 = Item::factory()->create(['status' => 'Open', 'start_date' => today()->addDays(2)]);

    $response = get('/upcoming');
    $content = $response->getContent();

    $response->assertSee($day1->title)->assertSee($day2->title);
    expect(strpos($content, $day1->title))->toBeLessThan(strpos($content, $day2->title));
});

it('includes items with no start_date but a future deadline in upcoming', function () {
    $deadlineOnly = Item::factory()->create(['status' => 'Open', 'start_date' => null, 'deadline' => today()->addDays(3)]);
    $noDate = Item::factory()->create(['status' => 'Open', 'start_date' => null, 'deadline' => null]);

    get('/upcoming')
        ->assertSee($deadlineOnly->title)
        ->assertDontSee($noDate->title);
});

it('groups deadline-only items by deadline date', function () {
    $deadlineItem = Item::factory()->create(['status' => 'Open', 'start_date' => null, 'deadline' => today()->addDays(4)]);
    $startDateItem = Item::factory()->create(['status' => 'Open', 'start_date' => today()->addDays(2), 'deadline' => null]);

    $response = get('/upcoming');
    $content = $response->getContent();

    $response->assertSee($startDateItem->title)->assertSee($deadlineItem->title);
    expect(strpos($content, $startDateItem->title))->toBeLessThan(strpos($content, $deadlineItem->title));
});

it('groups item with both start_date and deadline by start_date', function () {
    $item = Item::factory()->create([
        'status' => 'Open',
        'start_date' => today()->addDays(2),
        'deadline' => today()->addDays(5),
    ]);

    $response = get('/upcoming');

    $response->assertSee($item->title);
    $expectedHeader = today()->addDays(2)->format('l, F j');
    $response->assertSee($expectedHeader);
});

it('shows anytime items on /anytime', function () {
    $anytimeItem = Item::factory()->create(['status' => 'Open', 'start' => null, 'is_inbox' => false]);
    $somedayItem = Item::factory()->create(['status' => 'Open', 'start' => 'Someday', 'is_inbox' => false]);
    $inboxItem = Item::factory()->create(['status' => 'Open', 'start' => null, 'is_inbox' => true]);

    get('/anytime')
        ->assertSee($anytimeItem->title)
        ->assertDontSee($somedayItem->title)
        ->assertDontSee($inboxItem->title);
});

it('shows someday items on /someday', function () {
    $somedayItem = Item::factory()->create(['status' => 'Open', 'start' => 'Someday']);
    $anytimeItem = Item::factory()->create(['status' => 'Open', 'start' => null]);

    get('/someday')
        ->assertSee($somedayItem->title)
        ->assertDontSee($anytimeItem->title);
});

it('shows logged items on /logbook', function () {
    $loggedItem = Item::factory()->create(['is_logged' => true, 'completion_date' => now()]);
    $notLoggedItem = Item::factory()->create(['is_logged' => false]);

    get('/logbook')
        ->assertSee($loggedItem->title)
        ->assertDontSee($notLoggedItem->title);
});

it('groups logbook items by completion date descending', function () {
    $older = Item::factory()->create(['is_logged' => true, 'completion_date' => now()->subDays(2)]);
    $newer = Item::factory()->create(['is_logged' => true, 'completion_date' => now()->subDay()]);

    $response = get('/logbook');
    $content = $response->getContent();

    $response->assertSee($newer->title)->assertSee($older->title);
    expect(strpos($content, $newer->title))->toBeLessThan(strpos($content, $older->title));
});

it('shows checkmark icon for completed items in logbook', function () {
    Item::factory()->create(['is_logged' => true, 'status' => 'Completed', 'completion_date' => now()]);

    get('/logbook')->assertSee('evenodd');
});

it('shows x-mark icon for cancelled items in logbook', function () {
    Item::factory()->create(['is_logged' => true, 'status' => 'Cancelled', 'completion_date' => now()]);

    get('/logbook')->assertSee('evenodd');
});

it('shows trashed items on /trash', function () {
    $trashedItem = Item::factory()->create(['is_trashed' => true]);
    $openItem = Item::factory()->create(['is_trashed' => false]);

    get('/trash')
        ->assertSee($trashedItem->title)
        ->assertDontSee($openItem->title);
});
