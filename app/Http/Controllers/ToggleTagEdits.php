<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Native\Desktop\Facades\Settings;

class ToggleTagEdits extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        try {
            $current = Settings::get('allow_tag_edits', false);
            Settings::set('allow_tag_edits', ! $current);
        } catch (Exception) {
            $current = session('allow_tag_edits', false);
            session()->put('allow_tag_edits', ! $current);
        }

        return redirect()->route('settings');
    }
}
