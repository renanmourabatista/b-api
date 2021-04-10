<?php

namespace App\Data\Contracts\Repositories;

use App\Domain\Models\Transfer;

interface TransferRepository extends FindRepository
{
    public function create(array $params): Transfer;

    public function update(array $params, int $id): Transfer;

    public function get(int $id): Transfer;
}