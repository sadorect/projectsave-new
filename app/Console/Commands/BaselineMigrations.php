<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BaselineMigrations extends Command
{
    protected $signature = 'migrations:baseline {--through= : Mark all migrations up to and including this filename as already run} {--batch=1 : Batch number to assign}';

    protected $description = 'Mark existing migrations as already run for databases whose schema exists but migrations table is empty.';

    public function handle(): int
    {
        $through = $this->option('through');

        if (!$through) {
            $this->error('You must provide --through=<migration_name> so this command only baselines known-existing migrations.');

            return self::FAILURE;
        }

        if (!Schema::hasTable('migrations')) {
            $this->call('migrate:install');
        }

        if (DB::table('migrations')->count() > 0) {
            $this->error('The migrations table is not empty. This command only runs against an empty migrations table.');

            return self::FAILURE;
        }

        $files = collect(glob(database_path('migrations/*.php')))
            ->map(fn (string $path) => pathinfo($path, PATHINFO_FILENAME))
            ->sort()
            ->values();

        if (!$files->contains($through)) {
            $this->error("Migration [{$through}] was not found in database/migrations.");

            return self::FAILURE;
        }

        $selected = $files->filter(fn (string $migration) => strcmp($migration, $through) <= 0)->values();

        $this->warn('This will mark the selected migrations as already run without executing them.');
        $this->line('Last migration to baseline: ' . $through);
        $this->line('Migrations to insert: ' . $selected->count());

        if (!$this->confirm('Continue?', false)) {
            $this->info('Baseline cancelled.');

            return self::SUCCESS;
        }

        DB::table('migrations')->insert(
            $selected->map(fn (string $migration) => [
                'migration' => $migration,
                'batch' => (int) $this->option('batch'),
            ])->all()
        );

        $this->info('Baseline complete. You can now run php artisan migrate to apply only later migrations.');

        return self::SUCCESS;
    }
}