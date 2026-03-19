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

        try {
            Settings::set('api_token', $token);
        } catch (Exception) {
            session()->put('api_token', $token);
        }

        return redirect()->route('settings.index');
    }
}
