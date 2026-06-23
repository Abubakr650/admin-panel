<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

/**
 * تيم معدل بتيلويند - Tailwind Dynamic Theme Service
 */
class ThemeService
{
    protected static $colors = [
        'teal' => [
            'name' => 'Teal & Cyan',
            'from' => '#14b8a6', // teal-500
            'to' => '#0891b2',   // cyan-600
            'shadow' => 'rgba(20, 184, 166, 0.6)',
        ],
        'blue' => [
            'name' => 'Blue & Indigo',
            'from' => '#2563eb', // blue-600
            'to' => '#4338ca',   // indigo-700
            'shadow' => 'rgba(37, 99, 235, 0.6)',
        ],
        'purple' => [
            'name' => 'Purple & Indigo',
            'from' => '#9333ea', // purple-600
            'to' => '#4338ca',   // indigo-700
            'shadow' => 'rgba(147, 51, 234, 0.6)',
        ],
        'rose' => [
            'name' => 'Rose & Pink',
            'from' => '#f43f5e', // rose-500
            'to' => '#db2777',   // pink-600
            'shadow' => 'rgba(244, 63, 94, 0.6)',
        ],
        'orange' => [
            'name' => 'Orange & Amber',
            'from' => '#f97316', // orange-500
            'to' => '#d97706',   // amber-600
            'shadow' => 'rgba(249, 115, 22, 0.6)',
        ],
        'emerald' => [
            'name' => 'Emerald & Teal',
            'from' => '#10b981', // emerald-500
            'to' => '#0d9488',   // teal-600
            'shadow' => 'rgba(16, 185, 129, 0.6)',
        ],
        'indigo-violet' => [
            'name' => 'Indigo & Violet',
            'from' => '#4f46e5', // indigo-600
            'to' => '#7c3aed',   // violet-700
            'shadow' => 'rgba(79, 70, 229, 0.6)',
        ],
        'slate-blue' => [
            'name' => 'Slate & Blue',
            'from' => '#475569', // slate-600
            'to' => '#2563eb',   // blue-600
            'shadow' => 'rgba(71, 85, 105, 0.6)',
        ],
        'zinc-rose' => [
            'name' => 'Zinc & Rose',
            'from' => '#52525b', // zinc-600
            'to' => '#e11d48',   // rose-600
            'shadow' => 'rgba(82, 82, 91, 0.6)',
        ],
        'neutral-amber' => [
            'name' => 'Neutral & Amber',
            'from' => '#525252', // neutral-600
            'to' => '#d97706',   // amber-600
            'shadow' => 'rgba(82, 82, 82, 0.6)',
        ],
        'maroon-gold' => [
            'name' => 'Maroon & Gold',
            'from' => '#991b1b', // red-800
            'to' => '#fbbf24',   // amber-400
            'shadow' => 'rgba(153, 27, 27, 0.6)',
        ],
        'ocean-blue' => [
            'name' => 'Ocean & Deep Blue',
            'from' => '#0ea5e9', // sky-500
            'to' => '#1d4ed8',   // blue-700
            'shadow' => 'rgba(14, 165, 233, 0.6)',
        ],
        'lime-forest' => [
            'name' => 'Lime & Forest',
            'from' => '#84cc16', // lime-500
            'to' => '#166534',   // green-800
            'shadow' => 'rgba(132, 204, 22, 0.6)',
        ],
        'sunset-glow' => [
            'name' => 'Sunset Glow',
            'from' => '#f97316', // orange-500
            'to' => '#e11d48',   // rose-600
            'shadow' => 'rgba(249, 115, 22, 0.6)',
        ],
        'industrial-steel' => [
            'name' => 'Industrial Steel',
            'from' => '#334155', // slate-700
            'to' => '#0f172a',   // slate-950
            'shadow' => 'rgba(51, 65, 85, 0.6)',
        ],
    ];

    public function getCurrentThemeKey(): string
    {
        return Cache::rememberForever('system_theme_color', function () {
            return SystemSetting::where('key', 'theme_color')->value('value') ?? 'teal';
        });
    }

    public function getCurrentTheme(): array
    {
        $key = $this->getCurrentThemeKey();
        return self::$colors[$key] ?? self::$colors['teal'];
    }

    public function getAllThemes(): array
    {
        return self::$colors;
    }

    public function setTheme(string $key): bool
    {
        if (!isset(self::$colors[$key])) {
            return false;
        }

        SystemSetting::updateOrCreate(
            ['key' => 'theme_color'],
            ['value' => $key]
        );

        Cache::forget('system_theme_color');
        return true;
    }

    public function getCssVariables(): string
    {
        $theme = $this->getCurrentTheme();
        
        return "
            :root {
                --theme-from: {$theme['from']};
                --theme-to: {$theme['to']};
                --theme-shadow: {$theme['shadow']};
            }
        ";
    }
}
