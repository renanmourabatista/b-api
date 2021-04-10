<?php

namespace Tests\Unit\Data\Services;

use App\Data\Contracts\Repositories\TransferRepository;
use App\Domain\Models\Company;
use App\Domain\Models\Person;
use App\Domain\Models\Transfer;
use App\Domain\Models\User;
use App\Domain\Models\Wallet;
use Carbon\Carbon;
use App\Data\Contracts\Validator;
use App\Data\Services\CreateTransferService;
use App\Domain\UseCases\CreateTransfer;
use Mockery\Mock;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tests\TestCase;

class CreateTransferTest extends TestCase
{
    private CreateTransfer $service;

    private TransferRepository $repository;

    private Validator $validator;

    private User|Mock $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = \Mockery::mock(TransferRepository::class);
        $this->validator  = \Mockery::mock(Validator::class);

        $this->app->instance(TransferRepository::class, $this->repository);
        $this->app->instance(Validator::class, $this->validator);

        $this->user = $this->getUserToAuth();
        $this->actingAs($this->user);

        $this->service = $this->app->make(CreateTransferService::class);
    }

    /**
     * @test
     */
    public function shouldCreateATransfer()
    {
        Carbon::setTestNow($this->faker->dateTime);
        $date = Carbon::now();

        $params = $this->getDefaultParams();
        $params += [
            'value' => $this->faker->randomFloat(2, 0.01, 99),
            'date'  => $date
        ];

        $transferExpected = Transfer::factory()->make($params);

        $this->initializeValidatorGeneric();
        $this->setDefaultAmountFotUserWallet();

        $this->repository
            ->shouldReceive('create')
            ->with($params)
            ->andReturn($transferExpected)
            ->once();

        $result = $this->service->create($params);

        $this->assertEquals($transferExpected, $result);
    }

    private function setDefaultAmountFotUserWallet()
    {
        $walletAmount = $this->faker->randomFloat(2, 99, 999);

        $this->user
            ->person
            ->wallet
            ->allows('getTotalAmount')
            ->andReturn($walletAmount)
            ->once();
    }

    /**
     * @test
     */
    public function shouldValidateToTransfer()
    {
        $rules = [
            'value'              => 'required|min:0.01',
            'wallet_receiver_id' => 'required|exists:wallets,id',
        ];

        $messages = [
            'value.required'              => trans('messages.transfer.value.required'),
            'value.min'                   => trans('messages.transfer.value.min'),
            'wallet_receiver_id.required' => trans('messages.transfer.wallet_receiver_id.required'),
            'wallet_receiver_id.exists'   => trans('messages.transfer.wallet_receiver_id.exists')
        ];

        $transfer = \Mockery::mock(Transfer::class);

        $this->setDefaultAmountFotUserWallet();

        $this->repository
            ->shouldReceive('create')
            ->withAnyArgs()
            ->andReturn($transfer)
            ->once();

        $params = $this->getDefaultParams();

        $this->validator->shouldReceive('setRules')->with($rules)->once();
        $this->validator->shouldReceive('setMessages')->with($messages)->once();
        $this->validator->shouldReceive('validate')->with($params)->once();

        $this->service->create($params);
    }

    private function getUserToAuth(): User|Mock
    {
        $user                     = \Mockery::mock(User::class)->makePartial();
        $person                   = \Mockery::mock(Person::class)->makePartial();
        $person->company          = null;
        $user->person             = $person;
        $user->person->wallet     = \Mockery::mock(Wallet::class)->makePartial();
        $user->person->wallet->id = $this->faker->randomDigit();

        return $user;
    }

    private function initializeValidatorGeneric(): void
    {
        $this->validator->allows('setRules');
        $this->validator->allows('setMessages');
        $this->validator->allows('validate');
    }

    private function getDefaultParams(): array
    {
        return [
            'wallet_receiver_id' => $this->user->person->wallet->id + 1
        ];
    }

    /**
     * @test
     */
    public function shouldFailWhenUserIsAShopkeeper()
    {
        $this->expectException(AccessDeniedHttpException::class);
        $this->expectErrorMessage(trans('messages.transfer.store_owner.unauthorized'));
        $this->initializeValidatorGeneric();

        $user                  = $this->getUserToAuth();
        $user->person->company = \Mockery::mock(Company::class);

        $this->actingAs($user);
        $params = $this->getDefaultParams();

        $this->service->create($params);
    }

    /**
     * @test
     */
    public function shouldFailWhenUserTryToTransferToYourWallet()
    {
        $this->expectException(AccessDeniedHttpException::class);
        $this->expectErrorMessage(trans('messages.transfer.same_wallet.unauthorized'));
        $this->initializeValidatorGeneric();

        $user = $this->getUserToAuth();

        $this->actingAs($user);

        $params                       = $this->getDefaultParams();
        $params['wallet_receiver_id'] = $user->person->wallet->id;

        $this->service->create($params);
    }

    /**
     * @test
     */
    public function shouldFailWhenWalletHasNotFunds()
    {
        $this->expectException(AccessDeniedHttpException::class);
        $this->expectErrorMessage(trans('messages.transfer.value.insufficient_funds'));
        $this->initializeValidatorGeneric();

        $walletAmount    = $this->faker->randomFloat(2, 1, 999);
        $valueToTransfer = $walletAmount + $this->faker->randomFloat(2, 1);

        $this->user
            ->person
            ->wallet
            ->shouldReceive('getTotalAmount')
            ->andReturn($valueToTransfer)
            ->once();

        $params = $this->getDefaultParams();

        $params['value'] = $valueToTransfer;

        $this->service->create($params);
    }
}