<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\UseCases\CompleteTransfers as CompleteTransfersUseCase;

class AuthorizeTransfers extends Command
{
    protected $signature = 'authorize:transfers';

    protected $description = 'Complete all pending transfers';

    public function handle(CompleteTransfersUseCase $completeTransfers)
    {
        $completeTransfers->authorizePendingTransfers();
    }
}