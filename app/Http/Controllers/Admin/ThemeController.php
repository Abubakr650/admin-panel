<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ThemeService;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    public function update(Request $request)
    {
        $request->validate([
            'theme' => 'required|string',
        ]);

        if ($this->themeService->setTheme($request->theme)) {
            return back()->with('success', 'Theme color updated successfully!');
        }

        return back()->with('error', 'Theme not updated!');
    }
}
