<?php
namespace App\Domain\UseCases;

use App\Domain\Models\User;

interface CreateUser
{
    public function create(array $params): User;
}