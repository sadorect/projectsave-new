<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AdminAuditLog;
use Carbon\Carbon;

class PruneAuditLogs extends Command
{
    protected $signature = 'prune:audit-logs {days=90}';

    protected $description = 'Prune admin audit logs older than the given number of days (default 90)';

    public function handle()
    {
        $days = (int) $this->argument('days');
        $cutoff = Carbon::now()->subDays($days);

        $count = AdminAuditLog::where('created_at', '<', $cutoff)->delete();

        $this->info("Pruned {$count} audit log(s) older than {$days} days.");

        return 0;
    }
}
