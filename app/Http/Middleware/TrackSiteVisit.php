<?php

namespace App\Http\Middleware;

use App\Models\SiteVisitStat;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class TrackSiteVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldTrack($request)) {
            $sessionKey = 'analytics.site_visit_recorded.' . now()->toDateString();

            if (!$request->session()->has($sessionKey)) {
                $stat = SiteVisitStat::query()->firstOrCreate(
                    ['visit_date' => now()->toDateString()],
                    ['visits' => 0]
                );
                $stat->increment('visits');

                $request->session()->put($sessionKey, true);
            }
        }

        return $next($request);
    }

    private function shouldTrack(Request $request): bool
    {
        if (!$request->isMethod('GET')) {
            return false;
        }

        if ($request->ajax() || $request->expectsJson() || $request->isXmlHttpRequest()) {
            return false;
        }

        if ($request->is('admin*') || $request->routeIs('blog.calendar', 'feed')) {
            return false;
        }

        return $request->hasSession() && Schema::hasTable('site_visit_stats');
    }
}
