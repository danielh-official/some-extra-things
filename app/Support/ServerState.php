<?php

namespace App\Support;

use Native\Desktop\Facades\Settings;

class ServerState
{
    protected static bool $testingEnabled = false;

    public static function isEnabled(): bool
    {
        if (app()->runningUnitTests()) {
            return self::$testingEnabled;
        }

        return (bool) Settings::get('things_api.server_enabled', false);
    }

    public static function setEnabled(bool $enabled): void
    {
        if (app()->runningUnitTests()) {
            self::$testingEnabled = $enabled;

            return;
        }

        Settings::set('things_api.server_enabled', $enabled);
    }

    public static function toggle(): bool
    {
        $enabled = ! static::isEnabled();

        static::setEnabled($enabled);

        return $enabled;
    }
}

