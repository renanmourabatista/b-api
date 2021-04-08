<?php

namespace App\Providers;

use App\Data\Contracts\Validator;
use App\Helpers\ValidatorHelper;
use Illuminate\Support\ServiceProvider;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Validator::class, function($app)
        {
            return new ValidatorHelper();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
