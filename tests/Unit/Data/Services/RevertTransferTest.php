<?php

namespace Tests\Unit\Data\Services;

use App\Data\Contracts\Repositories\TransferRepository;
use App\Data\Services\RevertTransferService;
use App\Domain\Models\Person;
use App\Domain\Models\Transfer;
use App\Domain\Models\User;
use App\Domain\Models\Wallet;
use App\Data\Contracts\Validator;
use App\Domain\UseCases\CreateTransfer;
use App\Domain\UseCases\RevertTransfer;
use Illuminate\Support\Collection;
use Mockery\Mock;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tests\TestCase;

class RevertTransferTest extends TestCase
{
    private RevertTransfer $service;

    private CreateTransfer $createTransferService;

    private TransferRepository $repository;

    private Validator $validator;

    /**
     * @var Mock|User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository            = \Mockery::mock(TransferRepository::class);
        $this->createTransferService = \Mockery::mock(CreateTransfer::class);
        $this->validator             = \Mockery::mock(Validator::class);

        $this->app->instance(TransferRepository::class, $this->repository);
        $this->app->instance(Validator::class, $this->validator);
        $this->app->instance(CreateTransfer::class, $this->createTransferService);

        $this->user = $this->getUserToAuth();
        $this->actingAs($this->user);

        $this->service = $this->app->make(RevertTransferService::class);
    }

    /**
     * @return User|Mock
     */
    private function getUserToAuth()
    {
        $user                      = \Mockery::mock(User::class)->makePartial();
        $person                    = \Mockery::mock(Person::class)->makePartial();
        $person->company           = null;
        $user->person              = $person;
        $user->person->wallet      = \Mockery::mock(Wallet::class)->makePartial();
        $user->person->wallet->id  = $this->faker->randomDigit();
        $transfer                  = \Mockery::mock(Transfer::class)->makePartial();
        $transfer->id              = $this->faker->randomDigit();
        $transfer->wallet_payer_id = $this->faker->randomDigit();

        $user->person->wallet->transfersReceived = new Collection([$transfer]);

        return $user;
    }

    /**
     * @test
     */
    public function shouldRevertTransfer()
    {
        $transfer = $this->user->person->wallet->transfersReceived->first();

        $this->repository
            ->shouldReceive('get')
            ->with($transfer->id)
            ->andReturn($transfer)
            ->once();

        $params                         = [];
        $params['transfer_reverted_id'] = $transfer->id;
        $params['wallet_payee_id']      = $transfer->wallet_payer_id;
        $params['value']                = $transfer->value;

        $this->createTransferService
            ->shouldReceive('create')
            ->with($params)
            ->once();

        $this->service->revert($transfer->id);
    }

    /**
     * @test
     */
    public function shouldFailToRevertTransfer()
    {
        $this->expectException(AccessDeniedHttpException::class);
        $this->expectErrorMessage(trans('messages.transfer.revert.unauthorized'));

        $transfer = $this->user->person->wallet->transfersReceived->first();
        $id       = $transfer->id + 1;

        $this->service->revert($id);
    }
}