<?php
namespace App\Domain\UseCases;

use App\Domain\Models\Company;

interface GetWallet
{
    public function create(array $params): Company;
}