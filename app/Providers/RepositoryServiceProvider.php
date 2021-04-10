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
            return new TransferRepository();
        });

        $this->app->bind(WalletRepositoryContract::class, function($app)
        {
            return new WalletRepository();
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
