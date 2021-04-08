<?php

namespace App\Domain\Models;

use Domain\Models\Collections\Transfers;

class Wallet
{
    private int $id;

    private float $amount;

    private Transfers $transfers;

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getTransfers(): Transfers
    {
        return $this->transfers;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }
}