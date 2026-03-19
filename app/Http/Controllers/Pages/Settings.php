<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
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
            $apiToken = \Native\Desktop\Facades\Settings::get('api_token', null);
        } catch (Exception $e) {
            $theme = session('theme', 'system');
            $allowTagEdits = session('allow_tag_edits', false);
            $apiToken = session('api_token');
        }

        return view('settings', compact('theme', 'allowTagEdits', 'apiToken'));
    }
}
