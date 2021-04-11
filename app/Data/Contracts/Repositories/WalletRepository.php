<?php

namespace App\Data\Contracts\Repositories;

use App\Domain\Models\Wallet;

interface WalletRepository extends FindRepository
{
    public function get(int $idModel): Wallet;
}