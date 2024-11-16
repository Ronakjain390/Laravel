<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Events\QueryExecuted;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton('cache', function ($app) {
            return new LruCache($app['cache.store'], $maxSize = 1000);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Pagination
        $this->app->bind('paginator', function ($app, $data) {
            return new Paginator($data);
        });


        DB::listen(function ($query) {
            $sql = $query->sql;
            $bindings = $query->bindings;
            $time = $query->time;

            $message = vsprintf('[SQL] %s | Bindings: %s | Time: %s', [$sql, json_encode($bindings), $time]);

            Log::channel('info')->info($message);
        });
    }
}
