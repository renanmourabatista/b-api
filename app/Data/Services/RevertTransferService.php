<?php

namespace App\Data\Services;

use App\Data\Contracts\Repositories\TransferRepository;
use App\Data\Contracts\Validator;
use App\Domain\Models\Transfer;
use App\Domain\UseCases\CreateTransfer;
use App\Domain\UseCases\RevertTransfer;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RevertTransferService implements RevertTransfer
{
    private TransferRepository $repository;

    private Validator $validator;

    private CreateTransfer $createTransfer;

    public function __construct(
        TransferRepository $repository,
        Validator $validator,
        CreateTransfer $createTransfer
    ) {
        $this->repository     = $repository;
        $this->validator      = $validator;
        $this->createTransfer = $createTransfer;
    }

    public function revert(int $transferId): Transfer
    {
        $this->validate($transferId);
        $transfer = $this->repository->get($transferId);
        $params   = $this->createParams($transfer);

        return $this->createTransfer->create($params);
    }

    private function createParams(Transfer $transfer): array
    {
        $params                         = [];
        $params['transfer_reverted_id'] = $transfer->id;
        $params['wallet_payee_id']      = $transfer->wallet_payer_id;
        $params['value']                = $transfer->value;

        return $params;
    }

    private function validate(int $transferId): void
    {
        $validTransfers = auth()->user()->person->wallet->transfersReceived->pluck('id')->toArray();

        if (!in_array($transferId, $validTransfers)) {
            throw new AccessDeniedHttpException(trans('messages.transfer.revert.unauthorized'));
        }
    }
}