<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Native\Desktop\Facades\Settings;

class ToggleTagEdits extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): RedirectResponse
    {
        try {
            $current = Settings::get('allow_tag_edits', false);
            Settings::set('allow_tag_edits', ! $current);
        } catch (Exception) {
            $current = session('allow_tag_edits', false);
            session()->put('allow_tag_edits', ! $current);
        }

        return redirect()->route('settings.index');
    }
}
