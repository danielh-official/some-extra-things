<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;

class TrashItem extends Controller
{
    public function __invoke(Item $item): RedirectResponse
    {
        $item->update(['is_trashed' => true]);

        return redirect()->back();
    }
}
