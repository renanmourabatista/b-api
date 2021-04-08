<?php
namespace Domain\UseCases;

use App\Domain\Models\Company;

interface CreateCompany
{
    public function create(array $params): Company;
}