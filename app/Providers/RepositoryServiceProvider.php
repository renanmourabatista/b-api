<?php

namespace App\Providers;

use \App\Data\Contracts\Repositories\TransferRepository as TransferRepositoryContract;
use \App\Data\Contracts\Repositories\WalletRepository as WalletRepositoryContract;
use App\Repositories\TransferRepository;
use App\Repositories\WalletRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TransferRepositoryContract::class, function($app)
        {
            return  $app->make(TransferRepository::class);
        });

        $this->app->bind(WalletRepositoryContract::class, function($app)
        {
            return $app->make(WalletRepository::class);
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
