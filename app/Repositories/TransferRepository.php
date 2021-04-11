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

    public function get(int $idModel): Transfer
    {
        return $this->model()::find($idModel);
    }

    protected function exactlyMatchFields(): array
    {
        return ['status'];
    }

    public function update(array $params, int $idModel): bool
    {
        return $this->model()::find($idModel)->update($params);
    }
}