<?php 

use App\Models\Setting;



function setting($key, $default = null)
{
    $setting = Setting::where('key', $key)->first();
    return $setting?->value ?? $default;
}