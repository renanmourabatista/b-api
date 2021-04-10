<?php
namespace App\Data\Contracts\Repositories;

use App\Domain\Models\Person;

interface FindPersonRepository extends FindRepository
{
    public function get(int $id): Person;
}