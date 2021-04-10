<?php

namespace App\Data\Services;

use App\Data\Contracts\Repositories\CreateTransferRepository;
use App\Data\Contracts\Validator;
use App\Domain\Models\Transfer;
use App\Domain\UseCases\CreateTransfer;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class CreateTransferService implements CreateTransfer
{
    private CreateTransferRepository $repository;

    private Validator $validator;

    /**
     * CreateTransferService constructor.
     * @param CreateTransferRepository $repository
     * @param Validator $validator
     */
    public function __construct(CreateTransferRepository $repository, Validator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    public function create(array $params): Transfer
    {
        $this->validate($params);
        return $this->repository->create($params);
    }

    private function validate(array $params): void
    {
        $this->validateData($params);
        $this->validateUserAStoreOwner();
        $this->validateNewTransferIsToSameWalletOfUser($params);
    }

    private function validateData(array $params): void
    {
        $this->validator->setRules(
            [
                'value'              => 'required|min:0.01',
                'wallet_receiver_id' => 'required|exists:wallets,id',
            ]
        );

        $this->validator->setMessages(
            [
                'value.required'                     => trans('messages.transfer.value.required'),
                'value.min'                          => trans('messages.transfer.value.min'),
                'wallet_receiver_id.required'        => trans('messages.transfer.wallet_receiver_id.required'),
                'wallet_receiver_id.exists'          => trans('messages.transfer.wallet_receiver_id.exists')
            ]
        );

        $this->validator->validate($params);
    }

    private function validateUserAStoreOwner(): void
    {
        $user = auth()->user();

        if($user->person->isAnStoreOwner()) {
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
}