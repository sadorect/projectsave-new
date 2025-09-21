<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\AdminAuditLog;

class HandlerAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_handler_creates_audit_log_and_trims_trace()
    {
        // Ensure audit is enabled in non-production tests
        putenv('ERROR_AUDIT=1');

        // Ensure the env flag is set so Handler proceeds in test environment
        putenv('ERROR_AUDIT=1');
        $_ENV['ERROR_AUDIT'] = '1';

        // Generate a deep stack by recursive calls and throw an Exception at depth
        $ex = null;
        $generator = function ($depth) use (&$generator, &$ex) {
            if ($depth <= 0) {
                throw new \Exception('test');
            }
            return $generator($depth - 1);
        };

        try {
            $generator(100);
        } catch (\Exception $e) {
            $ex = $e;
        }

        // Run the application's exception handler report
        $handler = app(\App\Exceptions\Handler::class);
        $handler->report($ex);

        $log = AdminAuditLog::where('action', 'server_error')->first();
        $this->assertNotNull($log, 'AdminAuditLog record was not created');

        $meta = $log->meta ?? [];
        $this->assertArrayHasKey('trace', $meta, 'Meta does not contain trace');
        $this->assertLessThanOrEqual(50, count($meta['trace']), 'Trace was not trimmed to <= 50 frames');
        $this->assertArrayHasKey('fingerprint_seed', $meta, 'Meta missing fingerprint_seed');
        $this->assertNotEmpty($log->error_fingerprint, 'Error fingerprint is empty');
    }
}
