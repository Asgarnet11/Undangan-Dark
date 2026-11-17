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
        $key = $key ?: (string) config('themes.default', 'dark-boho');
        $defaults = self::defaultConfig($key);

        if (empty($overrides)) {
            return $defaults;
        }

        // If the stored config originated from another theme, purge incompatible visual tokens
        $storedTheme = $overrides['theme'] ?? null;
        if ($storedTheme !== null && $storedTheme !== $key) {
            unset(
                $overrides['preset'],
                $overrides['cover']['textColor'],
                $overrides['cover']['overlayColor'],
                $overrides['cover']['overlayOpacity']
            );
        }

        $config = array_replace_recursive($defaults, $overrides);
        $config['theme'] = $key;

        // Contrast and brightness safeguard per theme family
        $isLightHex = function (?string $hex): ?bool {
            if (! is_string($hex)) {
                return null;
            }
            $hex = ltrim($hex, '#');
            if (strlen($hex) === 3) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }
            if (strlen($hex) !== 6 || ! ctype_xdigit($hex)) {
                return null;
            }
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));

            return (($r * 299) + ($g * 587) + ($b * 114)) / 1000 >= 130;
        };

        if ($key === 'dark-boho') {
            // Dark Boho requires high-contrast light text on dark backgrounds
            $textColor = $config['cover']['textColor'] ?? null;
            if ($textColor !== null && $isLightHex($textColor) === false) {
                $config['cover']['textColor'] = '#efe6d8';
            }
            // Overlay color in dark-boho must be dark; if light (e.g. #fff8ec), reset to default dark
            $overlayColor = $config['cover']['overlayColor'] ?? null;
            if ($overlayColor !== null && $isLightHex($overlayColor) === true) {
                $config['cover']['overlayColor'] = '#1d1d1b';
                $config['cover']['overlayOpacity'] = 0.55;
            }
        } elseif ($key === 'sunny-meadow') {
            // Sunny Meadow requires high-contrast dark text on light backgrounds
            $textColor = $config['cover']['textColor'] ?? null;
            if ($textColor !== null && $isLightHex($textColor) === true) {
                $config['cover']['textColor'] = '#3b2f2a';
            }
            $overlayColor = $config['cover']['overlayColor'] ?? null;
            if ($overlayColor !== null && $isLightHex($overlayColor) === false) {
                $config['cover']['overlayColor'] = '#fff8ec';
                $config['cover']['overlayOpacity'] = 0.35;
            }
        }

        return $config;
    }
}
