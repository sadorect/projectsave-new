<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\SiteVisitStat;
use App\Services\AiImages\AiImageProviderManager;
use App\Services\AiImages\AiImageSettings;
use App\Contracts\ScansUploadedFiles;
use App\Services\MalwareScanner;
use App\Support\Navigation\NavigationBuilder;
use App\Support\SiteSettings;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
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
        $this->app->singleton(AiImageProviderManager::class, AiImageProviderManager::class);
        $this->app->singleton(AiImageSettings::class, AiImageSettings::class);
        $this->app->singleton(SiteSettings::class, SiteSettings::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        View::addNamespace('mail', resource_path('views/emails'));
        View::share('siteSettings', app(SiteSettings::class)->publicData());

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

        View::composer('components.layouts.footer', function ($view) {
            $totalSiteVisits = 0;
            $totalPostViews = 0;

            if (Schema::hasTable('site_visit_stats')) {
                $totalSiteVisits = (int) SiteVisitStat::query()->sum('visits');
            }

            if (Schema::hasTable('posts') && Schema::hasColumn('posts', 'view_count')) {
                $totalPostViews = (int) Post::query()->sum('view_count');
            }

            $view->with(compact('totalSiteVisits', 'totalPostViews'));
        });
    }
}
