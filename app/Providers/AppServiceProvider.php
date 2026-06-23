<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $themeService = app(\App\Services\ThemeService::class);
            $view->with('themeCss', $themeService->getCssVariables());
            $view->with('currentThemeKey', $themeService->getCurrentThemeKey());
            $view->with('allThemes', $themeService->getAllThemes());
        });
    }
}
