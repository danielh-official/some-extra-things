<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class Settings extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            $theme = \Native\Desktop\Facades\Settings::get('theme', 'system');
            $allowTagEdits = \Native\Desktop\Facades\Settings::get('allow_tag_edits', false);
        } catch (Exception $e) {
            $theme = session('theme', 'system');
            $allowTagEdits = session('allow_tag_edits', false);
        }

        return view('settings', compact('theme', 'allowTagEdits'));
    }
}
