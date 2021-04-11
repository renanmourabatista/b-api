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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class CreateTransferTest extends TestCase
{
    private CreateTransfer $service;

    private TransferRepository $repository;

    private Validator $validator;

    /**
     * @var Mock|User
     */
    private $user;

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
            'value'                => 'required',
            'wallet_payee_id'      => 'required|exists:wallets,id',
            'transfer_reverted_id' => 'nullable|exists:transfers,id'
        ];

        $messages = [
            'value.required'              => trans('messages.transfer.value.required'),
            'value.min'                   => trans('messages.transfer.value.min'),
            'wallet_payee_id.required'    => trans('messages.transfer.wallet_payee_id.required'),
            'wallet_payee_id.exists'      => trans('messages.transfer.wallet_payee_id.exists'),
            'transfer_reverted_id.exists' => trans('messages.transfer.transfer_reverted_id.exists')
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

    /**
     * @return User|Mock
     */
    private function getUserToAuth()
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
            'wallet_payee_id' => $this->user->person->wallet->id + 1,
            'wallet_payer_id' => $this->user->person->wallet->id,
            'status'          => Transfer::STATUS_PENDING,
            'value'           => 0.01
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

        $params                    = $this->getDefaultParams();
        $params['wallet_payee_id'] = $user->person->wallet->id;

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
            ->andReturn($walletAmount)
            ->once();

        $params = $this->getDefaultParams();

        $params['value'] = $valueToTransfer;

        $this->service->create($params);
    }

    /**
     * @test
     */
    public function shouldFailWhenTransferHasNotMinimalValue()
    {
        $this->expectException(BadRequestHttpException::class);
        $this->expectErrorMessage(trans('messages.transfer.value.min'));
        $this->initializeValidatorGeneric();

        $walletAmount = $this->faker->randomFloat(2, 1, 999);

        $this->user
            ->person
            ->wallet
            ->shouldReceive('getTotalAmount')
            ->andReturn($walletAmount)
            ->once();

        $params = $this->getDefaultParams();

        $params['value'] = 0.009;

        $this->service->create($params);
    }

    /**
     * @test
     */
    public function shouldFailWhenTryRevertANonRevertedTransfer()
    {
        $this->expectException(AccessDeniedHttpException::class);
        $this->expectErrorMessage(trans('messages.transfer.revert.unauthorized'));
        $this->initializeValidatorGeneric();

        $walletAmount = $this->faker->randomFloat(2, 1, 999);

        $this->user
            ->person
            ->wallet
            ->shouldReceive('getTotalAmount')
            ->andReturn($walletAmount)
            ->once();

        $params                         = $this->getDefaultParams();
        $params['transfer_reverted_id'] = $this->faker->randomDigit;

        $this->service->create($params);
    }
}