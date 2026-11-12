<?php

namespace App\Support;

use Illuminate\Support\Arr;

class ThemeRegistry
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return config('themes.themes', []);
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::all() as $key => $theme) {
            $options[$key] = (string) ($theme['name'] ?? $key);
        }

        return $options;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function find(?string $key): ?array
    {
        $key = $key ?: (string) config('themes.default', 'dark-boho');

        return Arr::get(self::all(), $key);
    }

    public static function exists(?string $key): bool
    {
        return self::find($key) !== null;
    }

    public static function viewName(?string $key): string
    {
        $theme = self::find($key);

        return (string) ($theme['view'] ?? 'themes.dark-boho.index');
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultConfig(?string $key): array
    {
        $theme = self::find($key);

        return (array) ($theme['default_config'] ?? []);
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function presets(?string $key): array
    {
        $theme = self::find($key);

        return (array) ($theme['presets'] ?? []);
    }

    /**
     * @param  array<string, mixed>|null  $overrides
     * @return array<string, mixed>
     */
    public static function resolveConfig(?string $key, ?array $overrides = null): array
    {
        $defaults = self::defaultConfig($key);

        if (empty($overrides)) {
            return $defaults;
        }

        return array_replace_recursive($defaults, $overrides);
    }
}
