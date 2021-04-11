<?php

namespace App\Providers;

use App\Data\Services\CompleteTransfersService;
use App\Data\Services\CreateTransferService;
use App\Data\Services\RevertTransferService;
use App\Domain\UseCases\CompleteTransfers;
use App\Domain\UseCases\CreateTransfer;
use App\Domain\UseCases\RevertTransfer;
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

        $this->app->bind(RevertTransfer::class, function($app)
        {
            return $app->make(RevertTransferService::class);
        });

        $this->app->bind(CompleteTransfers::class, function($app)
        {
            return $app->make(CompleteTransfersService::class);
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
