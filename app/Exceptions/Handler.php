<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Models\AdminAuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Existing reportable callbacks left intact.
        });
    }

    /**
     * Override report to persist server errors into admin_audit_logs when enabled.
     */
    public function report(Throwable $exception)
    {
        // Let the framework and any default reporters run first
        parent::report($exception);

        // Only proceed when audit logging is enabled via env or in production
        if (!app()->environment('production') && !env('ERROR_AUDIT', false)) {
            return;
        }

        try {
            // Determine HTTP status code when available
            $status = null;
            if ($exception instanceof HttpExceptionInterface) {
                $status = $exception->getStatusCode();
            }

            // We only want to record server errors (500+). If status exists and is <500, skip.
            if ($status !== null && $status < 500) {
                return;
            }

            // Compose context from the current request, if any
            $request = request();

            $meta = [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'status' => $status,
            ];

            // Limit trace size to avoid huge payloads
            $trace = $exception->getTrace();
            if (is_array($trace)) {
                $meta['trace'] = array_map(function ($t) {
                    return Arr::only($t, ['file', 'line', 'function', 'class']);
                }, array_slice($trace, 0, 50));
            }

            // Compute a simple fingerprint: exception class + top trace file/line + route/method
            $topFrame = null;
            if (!empty($trace) && is_array($trace)) {
                $topFrame = $trace[0] ?? null;
            }

            $route = null;
            try {
                $route = $request?->route()?->getName() ?? $request?->path();
            } catch (Throwable $_) {
                $route = null;
            }

            $fingerprintSeed = get_class($exception) . '|' . ($topFrame['file'] ?? '') . ':' . ($topFrame['line'] ?? '') . '|' . ($route ?? '') . '|' . ($request?->method() ?? '');
            $fingerprint = sha1($fingerprintSeed);

            // Deduplicate: look for an existing server_error with same fingerprint within window
            $windowMinutes = (int) env('ERROR_AUDIT_DEDUPE_WINDOW_MINUTES', 10);
            $since = now()->subMinutes($windowMinutes);

            $existing = AdminAuditLog::where('action', 'server_error')
                ->where('error_fingerprint', $fingerprint)
                ->where('created_at', '>=', $since)
                ->latest()
                ->first();

            if ($existing) {
                // increment counter inside meta if present, otherwise set count=2
                $existingMeta = $existing->meta ?? [];
                $existingMeta['count'] = isset($existingMeta['count']) ? ($existingMeta['count'] + 1) : 2;
                $existingMeta['last_seen_at'] = now()->toDateTimeString();
                $existing->meta = $existingMeta;
                $existing->ip_address = $request?->ip() ?? $existing->ip_address;
                $existing->user_agent = $request?->userAgent() ?? $existing->user_agent;
                $existing->save();
            } else {
                AdminAuditLog::create([
                    'admin_user_id' => Auth::id(),
                    'action' => 'server_error',
                    'target_type' => null,
                    'target_id' => null,
                    'meta' => array_merge($meta, ['count' => 1, 'fingerprint_seed' => $fingerprintSeed]),
                    'error_fingerprint' => $fingerprint,
                    'ip_address' => $request?->ip(),
                    'user_agent' => $request?->userAgent(),
                ]);
            }
        } catch (Throwable $e) {
            // If writing the audit log fails, write to the normal log but do not rethrow.
            Log::error('Failed to write admin audit log for exception: ' . $e->getMessage());
        }
    }
}
