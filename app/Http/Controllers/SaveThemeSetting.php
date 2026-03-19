<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Native\Desktop\Facades\Settings;

class SaveThemeSetting extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate(['theme' => 'required|in:system,light,dark']);

        try {
            Settings::set('theme', $request->theme);
        } catch (Exception) {
            session()->put('theme', $request->theme);
        }

        return redirect()->route('settings.index');
    }
}
