<?php
namespace App\Data\Contracts\Repositories;

use App\Domain\Models\Wallet;

interface FindWalletRepository extends FindRepository
{
    public function get(int $id): Wallet;
}