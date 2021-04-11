<?php

namespace App\Data\Services;

use App\Data\Contracts\Repositories\TransferRepository;
use App\Data\Contracts\Validator;
use App\Domain\Models\Transfer;
use App\Domain\UseCases\CreateTransfer;
use Illuminate\Contracts\Auth\Authenticatable;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

    private function getUser(): Authenticatable
    {
        return auth()->user();
    }

    public function create(array $params, bool $isRevert = false): Transfer
    {
        $this->validate($params, $isRevert);

        $params['wallet_payer_id'] = $this->getUser()->person->wallet->id;
        $params['status']          = Transfer::STATUS_PENDING;

        return $this->repository->create($params);
    }

    private function validate(array $params, bool $isRevert): void
    {
        $this->validateData($params);
        $this->validateUserAShopkeeper();
        $this->validateNewTransferIsToSameWalletOfUser($params);
        $this->validateWalletHasFundsToTransfer($params);
        $this->validateTransferHasMinimalValue($params);
        $this->validateRevertRules($isRevert, $params);
    }

    private function validateData(array $params): void
    {
        $this->validator->setRules(
            [
                'value'                => 'required',
                'wallet_payee_id'      => 'required|exists:wallets,id',
                'transfer_reverted_id' => 'nullable|exists:transfers,id'
            ]
        );

        $this->validator->setMessages(
            [
                'value.required'              => trans('messages.transfer.value.required'),
                'value.min'                   => trans('messages.transfer.value.min'),
                'wallet_payee_id.required'    => trans('messages.transfer.wallet_payee_id.required'),
                'wallet_payee_id.exists'      => trans('messages.transfer.wallet_payee_id.exists'),
                'transfer_reverted_id.exists' => trans('messages.transfer.transfer_reverted_id.exists')
            ]
        );

        $this->validator->validate($params);
    }

    private function validateUserAShopkeeper(): void
    {
        if ($this->getUser()->person->isAShopkeeper()) {
            throw new AccessDeniedHttpException(trans('messages.transfer.store_owner.unauthorized'));
        }
    }

    private function validateNewTransferIsToSameWalletOfUser(array $params): void
    {
        if ($this->getUser()->person->wallet->id === ($params['wallet_payee_id'] ?? null)) {
            throw new AccessDeniedHttpException(trans('messages.transfer.same_wallet.unauthorized'));
        }
    }

    private function validateWalletHasFundsToTransfer(array $params): void
    {
        if ($this->getUser()->person->wallet->getTotalAmount() < ($params['value'] ?? 0)) {
            throw new AccessDeniedHttpException(trans('messages.transfer.value.insufficient_funds'));
        }
    }

    private function validateTransferHasMinimalValue(array $params): void
    {
        $minimalValue = 0.01;

        if ( $minimalValue > (floatval($params['value'] ?? 0))) {
            throw new BadRequestHttpException(trans('messages.transfer.value.min'));
        }
    }

    private function validateRevertRules(bool $isRevert, array $params): void
    {
        if (!$isRevert && isset($params['transfer_reverted_id'])) {
            throw new AccessDeniedHttpException(trans('messages.transfer.revert.unauthorized'));
        }
    }
}