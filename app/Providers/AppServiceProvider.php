<?php

namespace App\Providers;

use App\Contracts\ScansUploadedFiles;
use App\Services\MalwareScanner;
use App\Support\Navigation\NavigationBuilder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ScansUploadedFiles::class, MalwareScanner::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        View::addNamespace('mail', resource_path('views/emails'));

        View::composer('admin.layouts.sidebar', function ($view) {
            $view->with(
                'adminNavigation',
                app(NavigationBuilder::class)->build('admin', request()->user())
            );
        });

        View::composer('layouts.content.sidebar', function ($view) {
            $view->with(
                'contentNavigation',
                app(NavigationBuilder::class)->build('content', request()->user())
            );
        });

        View::composer('components.layouts.lms', function ($view) {
            $view->with(
                'lmsNavigation',
                app(NavigationBuilder::class)->build('lms', request()->user())
            );
        });

        View::composer('components.layouts.asom-auth', function ($view) {
            $view->with(
                'lmsNavigation',
                app(NavigationBuilder::class)->build('lms', request()->user())
            );
        });

        View::composer('components.layouts.header', function ($view) {
            $view->with(
                'publicNavigation',
                app(NavigationBuilder::class)->build('public', request()->user())
            );
        });
    }
}
