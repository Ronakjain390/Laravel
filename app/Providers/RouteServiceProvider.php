<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            // API version 1 routes
            Route::middleware(['api', 'throttle:api_v1'])
                ->prefix('api/v1')
                ->namespace('App\Http\Controllers\V1')
                ->group(base_path('routes/api.php'));

            Route::middleware(['api', 'auth:admin-api', 'scopes:admin', 'throttle:api_v1'])
                ->prefix('api/v1/admin')
                ->name('admin.')
                ->namespace('App\Http\Controllers\V1')
                ->group(base_path('routes/api-admin-v1.php'));

            Route::middleware(['api', 'auth:user-api', 'scopes:user', 'throttle:api_v1'])
                ->prefix('api/v1/user')
                ->name('user.')
                ->namespace('App\Http\Controllers\V1')
                ->group(base_path('routes/api-user-v1.php'));

            // Web routes
            // if(!Auth::guard('user')->check()){
            //     dd(route::middleware('web'));
            //     Route::middleware(['web', 'throttle:web'])
            //     ->group(base_path('routes/web.php'));
            // }else{
            //     Route::get('/dashboard', function () {
            //         return view('user.dashboard.index');
            //     })->name('dashboard');
            // }
        // dd(Auth::getDefaultDriver(), Auth::guard('user')->user()->tokens()->where('name', Auth::getDefaultDriver())->get(), Auth::guard(Auth::getDefaultDriver())->user()->token());

            Route::middleware(['web', 'throttle:web','guest:user,admin,web,team-user'])
                ->group(base_path('routes/web.php'));

            Route::middleware(['web', 'auth:user,web,team-user', 'throttle:web'])
                ->group(base_path('routes/user.php'));

            Route::middleware(['web', 'auth:admin', 'throttle:web'])
                ->group(base_path('routes/admin.php'));
        });
    }

    /**
     * Configure the rate limiters for API and web routes.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        RateLimiter::for('api_v1', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        RateLimiter::for('web', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });
    }
}
