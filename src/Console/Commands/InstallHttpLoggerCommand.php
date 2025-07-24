<?php
// src/Console/Commands/InstallHttpLoggerCommand.php
namespace Flynns7\HttpLogger\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class InstallHttpLoggerCommand extends Command
{
    protected $signature = 'http-logger:install';
    protected $description = 'Set up the HTTP Logger (migrate & seed routes)';

    public function handle(): int
    {
        $this->info('Running migration for HTTP Logger...');
        Artisan::call('migrate', [
            '--path' => 'vendor/flynns7/http-logger/src/database/migrations'
        ]);
        $this->info(trim(Artisan::output()));

        $this->info('Seeding route data...');
        $seederPath = base_path('vendor/flynns7/http-logger/src/database/seeders/RoutesLogSeeder.php');
        if (file_exists($seederPath)) {
            require_once $seederPath;

            if (class_exists('RoutesLogSeeder')) {
                (new \RoutesLogSeeder)->run();
            } else {
                $this->error("RoutesLogSeeder class not found in file.");
            }
        } else {
            $this->error("RoutesLogSeeder file not found.");
        }
        $this->info(trim(Artisan::output()));

        $this->info('✅ HTTP Logger is installed and ready!');
        Cache::forever('http_logger_routes', DB::table('route_logs_mapping')->get()->keyBy('uri')->toArray());

        return Command::SUCCESS;
    }
}
