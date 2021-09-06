<?php

namespace App\Providers;

use App\Helpers\Junkifier;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('junkify', function ($data) {
            return app(Junkifier::class)->Junkify($data);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        foreach (glob(app()->basePath('app').'/Helpers/*.php') as $filename){
            require_once($filename);
        }
        $this->app->singleton(Junkifier::class, function ($app) {
            return new Junkifier();
        });
    }
}
