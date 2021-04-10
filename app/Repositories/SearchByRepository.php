<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait SearchByRepository
{
    protected function likableFields(): array
    {
        return [];
    }

    protected function exactlyMatchFields(): array
    {
        return [];
    }

    public function searchBy(array $parameters, int $page = 1, int $itemsPerPage = 10): LengthAwarePaginator
    {
        $queryBuilder = $this->model()::query();

        foreach ($parameters as $field => $value) {
            if (in_array($field, $this->exactlyMatchFields())) {
                $queryBuilder->where($field, '=', $value);
            }

            if (in_array($field, $this->likableFields())) {
                $queryBuilder->where($field, 'like', '%' . $value . '%');
            }
        }

        return $queryBuilder->orderBy('id', 'DESC')->paginate($itemsPerPage, ['*'], 'page',  $page);
    }
}