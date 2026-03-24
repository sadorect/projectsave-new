<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds security-related HTTP response headers to every web request.
 *
 * Register this in app/Http/Kernel.php → $middlewareGroups['web'].
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Prevent clickjacking – allow only same-origin framing
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Control referrer information sent to third parties
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restrict browser features not used by this application
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=()'
        );

        // Hide server/framework information
        $response->headers->set('X-Powered-By', '');
        $response->headers->remove('X-Powered-By');

        // Force HTTPS for 1 year
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Content Security Policy in report-only mode – tighten once all inline scripts/styles are audited
        $response->headers->set(
            'Content-Security-Policy-Report-Only',
            "default-src 'self'; " .
            "script-src 'self' https://www.google.com https://connect.facebook.net https://cdn.jsdelivr.net 'unsafe-inline'; " .
            "style-src 'self' https://cdnjs.cloudflare.com https://fonts.googleapis.com 'unsafe-inline'; " .
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
            "img-src 'self' data: https:; " .
            "frame-src https://www.youtube.com https://www.recaptcha.net https://www.google.com; " .
            "connect-src 'self';"
        );

        return $response;
    }
}
