<?php
namespace App\Domain\UseCases;

interface CompleteTransfers
{
    public function authorizePendingTransfers(): void;
    public function notifyAuthorizedTransfers(): void;
}