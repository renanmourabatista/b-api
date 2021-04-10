<?php

namespace App\Repositories;

use App\Domain\Models\Transfer;
use \App\Data\Contracts\Repositories\TransferRepository as TransferRepositoryContract;

class TransferRepository implements TransferRepositoryContract
{
    use SearchByRepository;

    public function model(): string
    {
        return Transfer::class;
    }

    public function create(array $params): Transfer
    {
        return $this->model()::create($params);
    }

    public function get(int $id): Transfer
    {
        return $this->model()::find($id);
    }

    protected function exactlyMatchFields(): array
    {
        return ['status'];
    }
}