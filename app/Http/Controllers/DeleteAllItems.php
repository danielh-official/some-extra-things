<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\RedirectResponse;

class DeleteAllItems extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): RedirectResponse
    {
        Item::query()->update(['is_trashed' => true]);

        return redirect()->route('settings.index');
    }
}
