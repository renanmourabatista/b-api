<?php

namespace App\Data\Services;

use App\Data\Contracts\Repositories\TransferRepository;
use App\Domain\Models\Transfer;
use App\Domain\UseCases\CompleteTransfers;
use Carbon\Carbon;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;

class CompleteTransfersService implements CompleteTransfers
{
    private TransferRepository $repository;

    private ClientInterface $api;

    const SERVICE_TRANSFER_AUTHORIZED = 'Autorizado';

    const SERVICE_TRANSFER_NOTIFIED = 'Enviado';

    public function __construct(
        TransferRepository $repository,
        ClientInterface $api
    ) {
        $this->repository = $repository;
        $this->api        = $api;
    }

    public function authorizePendingTransfers(int $page = 1): void
    {
        $itemsPerPage = 100;
        $paginator    = $this->repository->searchBy(['status' => Transfer::STATUS_PENDING], $page, $itemsPerPage);

        foreach ($paginator->items() as $transfer) {
            try {
                $this->authorize($transfer);
            } catch (ClientException $exception) {
                continue;
            }
        }

        if ($page === $paginator->lastPage()) {
            return;
        }

        $page++;
        $this->authorizePendingTransfers($page);
    }

    public function notifyAuthorizedTransfers(int $page = 1): void
    {
        $itemsPerPage = 100;
        $paginator    = $this->repository->searchBy(['status' => Transfer::STATUS_AUTHORIZED], $page, $itemsPerPage);

        foreach ($paginator->items() as $transfer) {
            try {
                $this->notify($transfer);
            } catch (ClientException $exception) {
                continue;
            }
        }

        if ($page === $paginator->lastPage()) {
            return;
        }

        $page++;
        $this->notifyAuthorizedTransfers($page);
    }

    private function notify(Transfer $transfer): void
    {
        if (!$transfer->payeeWallet->owner->isAShopkeeper()) {
            return;
        }

        $result = $this->api->request('get', config('services.transfer.notification'));

        $textResponse = $result->getBody()->getContents();
        $response     = json_decode($textResponse);

        if (isset($response->message) && $response->message === self::SERVICE_TRANSFER_NOTIFIED) {
            $this->repository->update(
                [
                    'notification_date' => Carbon::now()->toDateTimeString()
                ],
                $transfer->id
            );
        }
    }

    private function authorize(Transfer $transfer): void
    {
        $result = $this->api->request('get', config('services.transfer.authorization'));

        $textResponse = $result->getBody()->getContents();
        $response     = json_decode($textResponse);

        $this->authorized($response, $transfer);
        $this->notAuthorized($response, $transfer);
    }

    private function authorized(\stdClass $response, Transfer $transfer): void
    {
        if (isset($response->message) && $response->message === self::SERVICE_TRANSFER_AUTHORIZED) {
            $this->repository->update(['status' => Transfer::STATUS_AUTHORIZED], $transfer->id);
            $this->updateWalletsAmounts($transfer);
        }
    }

    private function notAuthorized(\stdClass $response, Transfer $transfer): void
    {
        if (isset($response->message) && $response->message !== self::SERVICE_TRANSFER_AUTHORIZED) {
            $this->repository->update(['status' => Transfer::STATUS_NOT_AUTHORIZED], $transfer->id);
        }
    }

    private function updateWalletsAmounts(Transfer $transfer): void
    {
        $payerWallet = $transfer->payerWallet;
        $payeeWallet = $transfer->payeeWallet;

        $payerAmount = $payerWallet->amount - $transfer->value;
        $payeeAmount = $payeeWallet->amount + $transfer->value;

        $payerWallet->update(['amount' => $payerAmount]);
        $payeeWallet->update(['amount' => $payeeAmount]);
    }
}
