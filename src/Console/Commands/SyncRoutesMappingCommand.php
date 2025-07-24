<?php

namespace Flynns7\HttpLogger\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class SyncRoutesMappingCommand extends Command
{
    protected $signature = 'http-logger:sync-routes-mapping {--force : Overwrite all case_name mappings}';
    protected $description = 'Sync Laravel routes into route_logs_mapping table without removing existing case_name mappings';

    public function handle(): int
    {
        $this->info('🔄 Syncing Laravel routes into route_logs_mapping...');

        $routes = collect(Route::getRoutes())->map(function ($route) {
            return [
                'uri'       => $route->uri(),
                'name'      => $route->getName(),
                'action'    => $route->getActionName(),
            ];
        });

        $bar = $this->output->createProgressBar(count($routes));
        $bar->start();

        foreach ($routes as $route) {
            $existing = DB::table('route_logs_mapping')->where('uri', $route['uri'])->first();

            DB::table('route_logs_mapping')->updateOrInsert(
                ['uri' => $route['uri']],
                [
                    'action'     => $route['action'],
                    'name'       => $route['name'],
                    'case_name'  => $this->option('force')
                        ? null
                        : ($existing->case_name ?? null),
                    'updated_at' => now(),
                    'created_at' => $existing->created_at ?? now(),
                ]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $cached = DB::table('route_logs_mapping')->get()->keyBy('uri')->toArray();
        cache()->forever('http_logger_routes', $cached);
        $this->info('📦 Cached updated route mappings');
        $this->info('✅ Sync complete.');

        return Command::SUCCESS;
    }
}
