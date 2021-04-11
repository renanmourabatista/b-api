<?php
namespace App\Domain\UseCases;

use App\Domain\Models\Transfer;

interface CreateTransfer
{
    public function create(array $params): Transfer;
}