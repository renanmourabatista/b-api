<?php
namespace App\Domain\Models;

class Person
{
    private string $cpf;

    private string $name;

    private Wallet $account;

    private Company $company;

    private function isAnStoreOwner(): bool
    {
        return $this->company !== null;
    }
}