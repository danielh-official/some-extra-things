<?php

use App\Models\SmartList;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('create renders the create view with an empty SmartList', function () {
    $response = get(route('smart-lists.create'));

    $response->assertSuccessful();
    $smartList = $response->viewData('smartList');
    expect($smartList)->toBeInstanceOf(SmartList::class);
    expect($smartList->exists)->toBeFalse();
});