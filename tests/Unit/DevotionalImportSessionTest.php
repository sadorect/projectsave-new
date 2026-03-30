<?php

namespace Tests\Unit;

use App\Models\DevotionalImportSession;
use Carbon\Carbon;
use Tests\TestCase;

class DevotionalImportSessionTest extends TestCase
{
    public function test_it_calculates_progress_and_stale_state_from_session_attributes(): void
    {
        Carbon::setTestNow('2026-03-30 12:00:00');

        $session = new DevotionalImportSession([
            'status' => DevotionalImportSession::STATUS_PROCESSING,
            'total_entries' => 1212,
            'processed_entries' => 606,
            'last_activity_at' => now()->subMinutes(12),
        ]);

        $this->assertSame(50, $session->progressPercentage());
        $this->assertTrue($session->isActive());
        $this->assertTrue($session->isStale());

        $session->forceFill([
            'status' => DevotionalImportSession::STATUS_COMPLETED,
            'last_activity_at' => now(),
        ]);

        $this->assertFalse($session->isActive());
        $this->assertFalse($session->isStale());

        Carbon::setTestNow();
    }
}
