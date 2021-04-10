<?php

namespace App\Repositories;

use App\Data\Contracts\Repositories\WalletRepository as WalletRepositoryContract;
use App\Domain\Models\Wallet;

class WalletRepository implements WalletRepositoryContract
{
    use SearchByRepository;

    public function model(): string
    {
        return Wallet::class;
    }

    public function get(int $id): Wallet
    {
        return $this->model()::find($id);
    }

    protected function exactlyMatchFields(): array
    {
        return ['status'];
    }
}