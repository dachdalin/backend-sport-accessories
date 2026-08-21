<?php

namespace App\Services;

use App\Models\BusinessSetting;

class BusinessSettingService
{
    /**
     * @var array<string, string>
     */
    private const DEFAULTS = [
        'site_name' => 'Sport Accessories Store',
        'logo' => 'def.png',
        'logo_storage_type' => 'public',
        'contact_email' => '',
        'contact_phone' => '',
        'address' => '',
        'currency_symbol' => '$',
        'minimum_order_amount' => '0',
        'free_delivery_over_amount' => '0',
        'tax_included_in_price' => '0',
        'maintenance_mode' => '0',
        'copyright_text' => '',
        'meta_title' => '',
        'meta_description' => '',
    ];

    /**
     * Get all business settings, filling in defaults for anything not yet stored.
     *
     * @return array<string, string>
     */
    public function all(): array
    {
        $stored = BusinessSetting::query()->pluck('value', 'key')->all();

        return array_merge(self::DEFAULTS, $stored);
    }

    /**
     * Persist a set of key/value pairs, creating or updating each row.
     *
     * @param  array<string, string>  $values
     */
    public function save(array $values): void
    {
        foreach ($values as $key => $value) {
            BusinessSetting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
