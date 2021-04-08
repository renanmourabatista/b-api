<?php
namespace App\Repositories;

use \App\Data\Contracts\Repositories\CreateTransferRepository;
use App\Domain\Models\Transfer;

class TransferRepository implements CreateTransferRepository
{
    public function model(): string
    {
        return Transfer::class;
    }

    public function create(array $params): Transfer
    {
        return $this->model()::create($params);
    }
}