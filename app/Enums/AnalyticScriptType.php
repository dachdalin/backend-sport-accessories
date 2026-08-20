<?php

namespace App\Enums;

enum AnalyticScriptType: string
{
    case GoogleAnalytics = 'google_analytics';
    case GoogleTagManager = 'google_tag_manager';
    case FacebookPixel = 'facebook_pixel';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::GoogleAnalytics => 'Google Analytics',
            self::GoogleTagManager => 'Google Tag Manager',
            self::FacebookPixel => 'Facebook Pixel',
            self::Custom => 'Custom',
        };
    }
}
