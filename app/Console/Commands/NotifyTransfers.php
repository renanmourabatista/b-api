<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\UseCases\CompleteTransfers as CompleteTransfersUseCase;

class NotifyTransfers extends Command
{
    protected $signature = 'notify:transfers';

    protected $description = 'Notify all transfers received to shopkeeper';

    public function handle(CompleteTransfersUseCase $completeTransfers)
    {
        $completeTransfers->notifyAuthorizedTransfers();
    }
}