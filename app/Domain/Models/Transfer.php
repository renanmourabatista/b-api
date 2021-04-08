<?php
namespace App\Domain\Models;

use Carbon\Carbon;

class Transfer
{
    protected int $id;

    protected float $value;

    protected Carbon $date;

    protected Wallet $sender;

    protected Wallet $receiver;
}