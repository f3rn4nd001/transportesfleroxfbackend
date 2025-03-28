<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $base_path=base_path('routes/Catalogo/');
        foreach (glob($base_path . "*") as $k => $filename){ 
        if(substr($filename,-4) == '.php'){
                Route::middleware('web')
                ->group($filename);
            }
        }
        $base_path2=base_path('routes/Sistemas/');
        foreach (glob($base_path2 . "*") as $k => $filename2){ 
        if(substr($filename2,-4) == '.php'){
                Route::middleware('web')
                ->group($filename2);
            }
        }

        $base_path2=base_path('routes/Autotransportes/');
        foreach (glob($base_path2 . "*") as $k => $filename2){ 
        if(substr($filename2,-4) == '.php'){
                Route::middleware('web')
                ->group($filename2);
            }
        }

        $base_path2=base_path('routes/Finanzas/');
        foreach (glob($base_path2 . "*") as $k => $filename2){ 
        if(substr($filename2,-4) == '.php'){
                Route::middleware('web')
                ->group($filename2);
            }
        }

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

        });
    }
}
