<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('redirects / to /all', function () {
    get('/')->assertRedirect('/all');
});
