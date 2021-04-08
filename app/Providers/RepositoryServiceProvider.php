<?php

namespace App\Providers;

use App\Data\Contracts\Repositories\CreateTransferRepository;
use App\Repositories\TransferRepository;
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
        $this->app->bind(CreateTransferRepository::class, function($app)
        {
            return new TransferRepository();
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
