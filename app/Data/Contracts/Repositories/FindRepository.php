<?php
namespace App\Data\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface FindRepository
{
    public function searchBy(array $parameters): LengthAwarePaginator;

    public function get(int $id): Model;
}