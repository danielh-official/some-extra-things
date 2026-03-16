<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects / to /all', function () {
    $this->get('/')->assertRedirect('/all');
});

it('returns 200 for /all', function () {
    $this->get('/all')->assertSuccessful();
});

it('returns 200 for /inbox', function () {
    $this->get('/inbox')->assertSuccessful();
});

it('returns 200 for /today', function () {
    $this->get('/today')->assertSuccessful();
});

it('returns 200 for /upcoming', function () {
    $this->get('/upcoming')->assertSuccessful();
});

it('returns 200 for /anytime', function () {
    $this->get('/anytime')->assertSuccessful();
});

it('returns 200 for /someday', function () {
    $this->get('/someday')->assertSuccessful();
});

it('returns 200 for /logbook', function () {
    $this->get('/logbook')->assertSuccessful();
});

it('returns 200 for /trash', function () {
    $this->get('/trash')->assertSuccessful();
});

it('shows inbox items on /inbox', function () {
    $inboxItem = Item::factory()->create(['is_inbox' => true, 'status' => 'Open']);
    $nonInboxItem = Item::factory()->create(['is_inbox' => false, 'status' => 'Open']);

    $this->get('/inbox')
        ->assertSee($inboxItem->title)
        ->assertDontSee($nonInboxItem->title);
});

it('shows today items on /today', function () {
    $todayItem = Item::factory()->create(['status' => 'Open', 'start_date' => today()]);
    $futureItem = Item::factory()->create(['status' => 'Open', 'start_date' => today()->addDay()]);
    $noDateItem = Item::factory()->create(['status' => 'Open', 'start_date' => null]);

    $this->get('/today')
        ->assertSee($todayItem->title)
        ->assertDontSee($futureItem->title)
        ->assertDontSee($noDateItem->title);
});

it('shows upcoming items on /upcoming', function () {
    $futureItem = Item::factory()->create(['status' => 'Open', 'start_date' => today()->addDay()]);
    $todayItem = Item::factory()->create(['status' => 'Open', 'start_date' => today()]);

    $this->get('/upcoming')
        ->assertSee($futureItem->title)
        ->assertDontSee($todayItem->title);
});

it('shows anytime items on /anytime', function () {
    $anytimeItem = Item::factory()->create(['status' => 'Open', 'start' => 'anytime', 'is_inbox' => false]);
    $somedayItem = Item::factory()->create(['status' => 'Open', 'start' => 'someday', 'is_inbox' => false]);
    $inboxItem = Item::factory()->create(['status' => 'Open', 'start' => 'anytime', 'is_inbox' => true]);

    $this->get('/anytime')
        ->assertSee($anytimeItem->title)
        ->assertDontSee($somedayItem->title)
        ->assertDontSee($inboxItem->title);
});

it('shows someday items on /someday', function () {
    $somedayItem = Item::factory()->create(['status' => 'Open', 'start' => 'someday']);
    $anytimeItem = Item::factory()->create(['status' => 'Open', 'start' => 'anytime']);

    $this->get('/someday')
        ->assertSee($somedayItem->title)
        ->assertDontSee($anytimeItem->title);
});

it('shows logged items on /logbook', function () {
    $loggedItem = Item::factory()->create(['is_logged' => true]);
    $notLoggedItem = Item::factory()->create(['is_logged' => false]);

    $this->get('/logbook')
        ->assertSee($loggedItem->title)
        ->assertDontSee($notLoggedItem->title);
});

it('shows cancelled items on /trash', function () {
    $cancelledItem = Item::factory()->create(['status' => 'Cancelled']);
    $openItem = Item::factory()->create(['status' => 'Open']);

    $this->get('/trash')
        ->assertSee($cancelledItem->title)
        ->assertDontSee($openItem->title);
});
