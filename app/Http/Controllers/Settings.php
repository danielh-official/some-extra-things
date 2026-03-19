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
        } catch (Exception $e) {
            $theme = session('theme', 'system');
        }

        return view('settings', compact('theme'));
    }
}
