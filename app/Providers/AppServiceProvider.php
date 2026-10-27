<?php

namespace App\Providers;

use App\Services\GitHubLanguageService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(GitHubLanguageService::class, fn () => new GitHubLanguageService(
            config('services.github.username'),
            config('services.github.token'),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
