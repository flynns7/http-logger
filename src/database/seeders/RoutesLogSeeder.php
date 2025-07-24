<?php
// src/database/seeders/RoutesLogSeeder.php
namespace Flynns7\HttpLogger\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class RoutesLogSeeder extends Seeder
{
    public function run(): void
    {
        $routes = collect(Route::getRoutes())->map(function ($route) {
            return [
                'uri'       => $route->uri(),
                'name'      => $route->getName(),
                'action'    => $route->getActionName(),
                'case_name' => null, // Optional or generate based on logic
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        DB::table('route_logs_mapping')->truncate(); // Optional for reseed
        DB::table('route_logs_mapping')->insert($routes->toArray());
    }
}
