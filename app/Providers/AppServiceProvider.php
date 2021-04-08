<?php

namespace App\Providers;

use App\Data\Services\CreateTransferService;
use App\Domain\UseCases\CreateTransfer;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CreateTransfer::class, function($app)
        {
            return new CreateTransferService(

            );
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
