<?php
namespace App\Domain\UseCases;

use App\Domain\Models\Wallet;

interface GetWallet
{
    public function get(int $id): Wallet;
}