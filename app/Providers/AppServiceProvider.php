<?php

namespace App\Providers;

use App\Data\Services\CreateTransferService;
use App\Domain\UseCases\CreateTransfer;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
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
        $this->app->bind(ClientInterface::class, function($app)
        {
            return $app->make(Client::class);
        });

        $this->app->bind(CreateTransfer::class, function($app)
        {
            return $app->make(CreateTransferService::class);
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
