<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\RedirectResponse;

class PermanentlyDeleteTrashedItems extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): RedirectResponse
    {
        Item::where('is_trashed', true)->delete();

        return redirect()->route('trash.index');
    }
}
