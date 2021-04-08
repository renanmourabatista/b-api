<?php

namespace App\Data\Services;

use App\Data\Contracts\Repositories\CreateTransferRepository;
use App\Data\Contracts\Validator;
use App\Domain\Models\Transfer;
use App\Domain\UseCases\CreateTransfer;

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
        $this->validator->setRules(
            [
                'value'              => 'required|min:0.01',
                'wallet_sender_id'   => 'required|exists:wallets,id',
                'wallet_receiver_id' => 'required|exists:wallets,id|not_same_wallet',
            ]
        );

        $this->validator->setMessages(
            [
                'value.required'                     => trans('transfer.value.required'),
                'value.min'                          => trans('transfer.value.min'),
                'wallet_sender_id.required'          => trans('transfer.wallet_sender_id.required'),
                'wallet_sender_id.exists'            => trans('transfer.wallet_sender_id.exists'),
                'wallet_receiver_id.required'        => trans('transfer.wallet_sender_id.required'),
                'wallet_receiver_id.exists'          => trans('transfer.wallet_sender_id.exists'),
                'wallet_receiver_id.not_same_wallet' => trans('transfer.wallet_sender_id.exists'),
            ]
        );

        $this->validator->validate($params);
    }
}