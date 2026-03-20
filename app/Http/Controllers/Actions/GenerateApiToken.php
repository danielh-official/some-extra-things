<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Native\Desktop\Facades\Settings;

class GenerateApiToken extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): RedirectResponse
    {
        $token = bin2hex(random_bytes(32));
        $hash = hash('sha256', $token);

        try {
            Settings::set('api_token_hash', $hash);
        } catch (Exception) {
            session()->put('api_token_hash', $hash);
        }

        return redirect()->route('settings.index')->with('new_api_token', $token);
    }
}
