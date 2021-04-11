<?php

namespace Tests\Unit\Data\Services;

use App\Data\Contracts\Repositories\TransferRepository;
use App\Data\Contracts\Repositories\WalletRepository;
use App\Data\Services\CompleteTransfersService;
use App\Domain\Models\Person;
use App\Domain\Models\Transfer;
use App\Domain\Models\User;
use App\Domain\Models\Wallet;
use App\Domain\UseCases\CompleteTransfers;
use Carbon\Carbon;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Mockery\MockInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

class CompleteTransfersTest extends TestCase
{
    private TransferRepository $repository;

    private WalletRepository $walletRepository;

    private CompleteTransfers $service;

    private ClientInterface $api;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository       = \Mockery::mock(TransferRepository::class);
        $this->walletRepository = \Mockery::mock(WalletRepository::class);
        $this->api              = \Mockery::mock(ClientInterface::class);

        $this->app->instance(TransferRepository::class, $this->repository);
        $this->app->instance(WalletRepository::class, $this->walletRepository);
        $this->app->instance(ClientInterface::class, $this->api);

        $this->service = $this->app->make(CompleteTransfersService::class);
    }

    /**
     * @test
     */
    public function shouldAuthorizePendingTransfers()
    {
        $transfer = $this->getTransferMock();
        $items    = new Collection([$transfer]);

        $page         = 1;
        $lastPage     = $this->faker->numberBetween(1, 9);
        $itemsPerPage = 100;

        $paginator = $this->getPaginatorMock($items, $lastPage);
        $this->shouldFindItemsPerPage($page, $lastPage, $itemsPerPage, $paginator, Transfer::STATUS_PENDING);
        $this->shouldGetAuthorizationFromExternalApi($lastPage);
        $this->shouldUpdateStatusOfTransfer($transfer, $items, Transfer::STATUS_AUTHORIZED, $lastPage);
        $this->shouldUpdateWalletsAmounts($transfer, $items, $lastPage);

        $this->service->authorizePendingTransfers();
    }

    /**
     * @test
     */
    public function shouldNotAuthorizePendingTransfers()
    {
        $transfer = $this->getTransferMock();
        $items    = new Collection([$transfer]);

        $page         = 1;
        $lastPage     = $this->faker->numberBetween(1, 9);
        $itemsPerPage = 100;

        $paginator = $this->getPaginatorMock($items, $lastPage);
        $this->shouldFindItemsPerPage($page, $lastPage, $itemsPerPage, $paginator, Transfer::STATUS_PENDING);
        $this->shouldGetUnauthorizedFromExternalApi($lastPage);
        $this->shouldUpdateStatusOfTransfer($transfer, $items, Transfer::STATUS_NOT_AUTHORIZED, $lastPage);

        $this->service->authorizePendingTransfers();
    }

    /**
     * @test
     */
    public function shouldNotifyAuthorizedTransfers()
    {
        $transfer                       = $this->getTransferMock();
        $items                          = new Collection([$transfer]);
        $transfer->payeeWallet->owner = \Mockery::mock(Person::class);
        $transfer->payeeWallet
            ->owner
            ->shouldReceive('isAShopkeeper')
            ->andReturn(true);

        Carbon::setTestNow($this->faker->dateTime);

        $page         = 1;
        $lastPage     = $this->faker->numberBetween(1, 9);
        $itemsPerPage = 100;

        $paginator = $this->getPaginatorMock($items, $lastPage);
        $this->shouldFindItemsPerPage($page, $lastPage, $itemsPerPage, $paginator, Transfer::STATUS_AUTHORIZED);
        $this->shouldCheckNotificationHasSendByExternalApi($lastPage);
        $this->shouldUpdateDateNotificationOfTransfer($transfer, $items, $lastPage);

        $this->service->notifyAuthorizedTransfers();
    }

    /**
     * @test
     */
    public function shouldNotUpdateTransferNotificationDateWhenUserIsNotAShopkeeper()
    {
        $transfer                       = $this->getTransferMock();
        $items                          = new Collection([$transfer]);
        $transfer->payeeWallet->owner = \Mockery::mock(User::class);
        $transfer->payeeWallet
            ->owner
            ->shouldReceive('isAShopkeeper')
            ->andReturn(false);

        $page         = 1;
        $lastPage     = $this->faker->numberBetween(1, 9);
        $itemsPerPage = 100;

        $paginator = $this->getPaginatorMock($items, $lastPage);
        $this->shouldFindItemsPerPage($page, $lastPage, $itemsPerPage, $paginator, Transfer::STATUS_AUTHORIZED);

        $this->service->notifyAuthorizedTransfers();
    }

    /**
     * @test
     */
    public function shouldNotUpdateTransferNotificationDateWhenServiceIsOffline()
    {
        $transfer                       = $this->getTransferMock();
        $items                          = new Collection([$transfer]);
        $transfer->payeeWallet->owner = \Mockery::mock(User::class);
        $transfer->payeeWallet
            ->owner
            ->shouldReceive('isAShopkeeper')
            ->andReturn(true);

        $page         = 1;
        $lastPage     = $this->faker->numberBetween(1, 9);
        $itemsPerPage = 100;

        $paginator = $this->getPaginatorMock($items, $lastPage);
        $this->shouldFindItemsPerPage($page, $lastPage, $itemsPerPage, $paginator, Transfer::STATUS_AUTHORIZED);
        $this->shouldThrowGuzzleException($lastPage, config('services.transfer.notification'));

        $this->service->notifyAuthorizedTransfers();
    }

    /**
     * @test
     */
    public function shouldNotAuthorizeTransferWhenServiceIsOffline()
    {
        $transfer = $this->getTransferMock();
        $items    = new Collection([$transfer]);

        $page         = 1;
        $lastPage     = $this->faker->numberBetween(1, 9);
        $itemsPerPage = 100;

        $paginator = $this->getPaginatorMock($items, $lastPage);
        $this->shouldFindItemsPerPage($page, $lastPage, $itemsPerPage, $paginator, Transfer::STATUS_PENDING);
        $this->shouldThrowGuzzleException($lastPage, config('services.transfer.authorization'));

        $this->service->authorizePendingTransfers();
    }

    private function shouldUpdateWalletsAmounts(Transfer $transfer, Collection $items, int $lastPage): void
    {
        $valueToPayer   = $transfer->payerWallet->amount - $transfer->value;
        $valueToPayee = $transfer->payeeWallet->amount + $transfer->value;

        $transfer->payeeWallet
            ->shouldReceive('update')
            ->with(['amount' => $valueToPayee])
            ->times($items->count() * $lastPage);

        $transfer->payerWallet
            ->shouldReceive('update')
            ->with(['amount' => $valueToPayer])
            ->times($items->count() * $lastPage);;
    }

    private function getTransferMock(): Transfer
    {
        $transfer                         = \Mockery::mock(Transfer::class)->makePartial();
        $transfer->id                     = $this->faker->randomDigit;
        $transfer->value                  = $this->faker->randomFloat(2, 1, 999);
        $transfer->payerWallet           = \Mockery::mock(Wallet::class)->makePartial();
        $transfer->payeeWallet         = \Mockery::mock(Wallet::class)->makePartial();
        $transfer->payerWallet->amount   = $transfer->value + $this->faker->randomFloat(2, 1, 999);
        $transfer->payeeWallet->amount = 0;

        return $transfer;
    }

    private function getPaginatorMock(Collection $items, $lastPage): LengthAwarePaginator
    {
        $paginator = \Mockery::mock(LengthAwarePaginator::class);

        $paginator
            ->shouldReceive('items')
            ->andReturn($items)
            ->times($lastPage);

        $paginator
            ->shouldReceive('lastPage')
            ->andReturn($lastPage)
            ->times($lastPage);

        return $paginator;
    }

    private function shouldFindItemsPerPage(
        int $page,
        int $lastPage,
        int $itemsPerPage,
        LengthAwarePaginator $paginator,
        int $status
    ): void {
        while ($page <= $lastPage) {
            $this->repository
                ->shouldReceive('searchBy')
                ->with(['status' => $status], $page, $itemsPerPage)
                ->andReturn($paginator);

            $page++;
        }
    }

    private function shouldGetAuthorizationFromExternalApi(int $lastPage): void
    {
        $mockResponse = \Mockery::mock(ResponseInterface::class);
        $mockBody     = \Mockery::mock();
        $mockBody
            ->shouldReceive('getContents')
            ->andReturn('{ "message" : "Autorizado" }')
            ->times($lastPage);

        $this->shouldRequestAPi($mockBody, $mockResponse, $lastPage, config('services.transfer.authorization'));
    }

    private function shouldGetUnauthorizedFromExternalApi(int $lastPage): void
    {
        $mockResponse = \Mockery::mock(ResponseInterface::class);
        $mockBody     = \Mockery::mock();
        $mockBody
            ->shouldReceive('getContents')
            ->andReturn('{ "message" : "Não Autorizado" }')
            ->times($lastPage);

        $this->shouldRequestAPi($mockBody, $mockResponse, $lastPage, config('services.transfer.authorization'));
    }

    private function shouldCheckNotificationHasSendByExternalApi(int $lastPage): void
    {
        $mockResponse = \Mockery::mock(ResponseInterface::class);
        $mockBody     = \Mockery::mock();
        $mockBody
            ->shouldReceive('getContents')
            ->andReturn('{ "message" : "Enviado" }')
            ->times($lastPage);

        $this->shouldRequestAPi($mockBody, $mockResponse, $lastPage, config('services.transfer.notification'));
    }

    private function shouldThrowGuzzleException(int $lastPage, string $route): void
    {
        $mockResponse = \Mockery::mock(ResponseInterface::class);
        $mockBody     = \Mockery::mock();
        $request      = \Mockery::mock(RequestInterface::class);
        $response     = \Mockery::mock(ResponseInterface::class);
        $response->allows('getStatusCode');

        $mockBody
            ->shouldReceive('getContents')
            ->andThrow(new ClientException('', $request, $response))
            ->times($lastPage);

        $this->shouldRequestAPi($mockBody, $mockResponse, $lastPage, $route);
    }

    private function shouldRequestAPi(
        MockInterface $mockBody,
        MockInterface $mockResponse,
        int $lastPage,
        string $route
    ): void {
        $mockResponse
            ->shouldReceive('getBody')
            ->andReturn($mockBody)
            ->times($lastPage);

        $this->api
            ->shouldReceive('request')
            ->with('get', $route)
            ->andReturn($mockResponse)
            ->times($lastPage);
    }

    private function shouldUpdateStatusOfTransfer(
        Transfer $transfer,
        Collection $items,
        int $status,
        int $lastPage
    ): void {
        $this->repository
            ->shouldReceive('update')
            ->with([
                       'status' => $status,
                   ],
                   $transfer->id
            )
            ->andReturn(true)
            ->times($items->count() * $lastPage);
    }

    private function shouldUpdateDateNotificationOfTransfer(Transfer $transfer, Collection $items, int $lastPage): void
    {
        $this->repository
            ->shouldReceive('update')
            ->with([
                       'notification_date' => Carbon::now()->toDateTimeString(),
                   ],
                   $transfer->id
            )
            ->andReturn(true)
            ->times($items->count() * $lastPage);
    }
}