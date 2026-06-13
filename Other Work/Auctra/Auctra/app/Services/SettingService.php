<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    protected $cachePrefix = 'setting_';


    public function all()
    {
        return Cache::remember(
            $this->cachePrefix . 'all',
            60,
            function () {
                return Setting::pluck('value', 'key');
            }
        );
    }

    public function get(string $key, $default = null)
    {
        return Cache::remember(
            $this->cachePrefix . $key,
            60,
            function () use ($key, $default) {
                $setting = Setting::where('key', $key)->first();
                return $setting?->value ?? $default;
            }
        );
    }

    public function getSection(string $key)
    {
        $value = $this->get($key);
        return is_string($value) ? json_decode($value, true) : $value;
    }

    public function set(string $key, $value)
    {
        $setting = Setting::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : $value]
        );

        Cache::forget($this->cachePrefix . $key);

        return $setting;
    }

    public function setMany(array $data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    // ? Upload images for a setting
    public function uploadSettingImages($setting, $images)
    {
        foreach ($images as $index => $image) {
            $setting->addMedia($image)
                ->toMediaCollection('images');
        }
    }

    // ? Set multiple settings with images
    public function setManyWithImages(array $data)
    {
        foreach ($data as $key => $item) {
            $setting = $this->set($key, $item['value']);

            if (!empty($item['images'])) {
                $this->uploadSettingImages($setting, $item['images']);
            }
        }
    }


    public function toggle(string $key)
    {
        $currentValue = $this->get($key, '0');
        $newValue = $currentValue === '1' ? '0' : '1';
        return $this->set($key, $newValue);
    }
    
}