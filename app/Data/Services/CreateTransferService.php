<?php

namespace App\Data\Services;

use App\Data\Contracts\Repositories\TransferRepository;
use App\Data\Contracts\Validator;
use App\Domain\Models\Transfer;
use App\Domain\UseCases\CreateTransfer;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CreateTransferService implements CreateTransfer
{
    private TransferRepository $repository;

    private Validator $validator;

    /**
     * CreateTransferService constructor.
     * @param TransferRepository $repository
     * @param Validator $validator
     */
    public function __construct(TransferRepository $repository, Validator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    public function create(array $params): Transfer
    {
        $this->validate($params);

        $params['wallet_sender_id'] = auth()->user()->person->wallet->id;

        return $this->repository->create($params);
    }

    private function validate(array $params): void
    {
        $this->validateData($params);
        $this->validateUserAShopkeeper();
        $this->validateNewTransferIsToSameWalletOfUser($params);
        $this->validateWalletHasFundsToTransfer($params);
    }

    private function validateData(array $params): void
    {
        $this->validator->setRules(
            [
                'value'                => 'required|min:0.01',
                'wallet_receiver_id'   => 'required|exists:wallets,id',
                'transfer_reverted_id' => 'nullable|exists:transfers,id'
            ]
        );

        $this->validator->setMessages(
            [
                'value.required'                     => trans('messages.transfer.value.required'),
                'value.min'                          => trans('messages.transfer.value.min'),
                'wallet_receiver_id.required'        => trans('messages.transfer.wallet_receiver_id.required'),
                'wallet_receiver_id.exists'          => trans('messages.transfer.wallet_receiver_id.exists'),
                'transfer_reverted_id.exists'        => trans('messages.transfer.transfer_reverted_id.exists')
            ]
        );

        $this->validator->validate($params);
    }

    private function validateUserAShopkeeper(): void
    {
        $user = auth()->user();

        if($user->person->isAShopkeeper()) {
            throw new AccessDeniedHttpException(trans('messages.transfer.store_owner.unauthorized'));
        }
    }

    private function validateNewTransferIsToSameWalletOfUser(array $params): void
    {
        $user = auth()->user();

        if($user->person->wallet->id === ($params['wallet_receiver_id'] ?? null)) {
            throw new AccessDeniedHttpException(trans('messages.transfer.same_wallet.unauthorized'));
        }
    }

    private function validateWalletHasFundsToTransfer(array $params): void
    {
        $user = auth()->user();

        if($user->person->wallet->getTotalAmount() <= ($params['value'] ?? 0)) {
            throw new AccessDeniedHttpException(trans('messages.transfer.value.insufficient_funds'));
        }
    }
}