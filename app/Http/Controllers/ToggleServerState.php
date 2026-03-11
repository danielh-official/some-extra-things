<?php

namespace App\Http\Controllers;

use App\Support\ServerState;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ToggleServerState extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $enabled = ServerState::toggle();

        return redirect()
            ->back()
            ->with('server_enabled', $enabled);
    }
}

