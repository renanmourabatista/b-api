<?php

namespace App\Data\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface FindRepository extends BaseRepository
{
    public function searchBy(array $parameters, int $page = 1, int $itemsPerPage = 10): LengthAwarePaginator;

    public function get(int $id): Model;
}