<?php
namespace App\Domain\UseCases;

use App\Domain\Models\Transfer;

interface RevertTransfer
{
    public function revert(Transfer $transfer): Transfer;
}