<?php
namespace App\Data\Contracts\Repositories;

use App\Domain\Models\Transfer;

interface CreateTransferRepository
{
    public function create(array $params): Transfer;
}