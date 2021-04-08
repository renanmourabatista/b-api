<?php

namespace Tests\Unit\Data\Services;

use Carbon\Carbon;
use Data\Contracts\Repositories\CreateTransferRepository;
use Data\Contracts\Validator;
use Data\Dto\TransferDto;
use Data\Services\CreateTransferService;
use Domain\Models\Wallet;
use Domain\UseCases\CreateTransfer;
use Tests\TestCase;

class CreateTransferTest extends TestCase
{
    private CreateTransfer $service;

    private CreateTransferRepository $repository;

    private Validator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = \Mockery::mock(CreateTransferRepository::class);
        $this->validator = \Mockery::mock(Validator::class);

        $this->service = new CreateTransferService($this->repository, $this->validator);
    }

    /**
     * @test
     */
    public function shouldCreateATransfer()
    {
        $params = [
            'value'              => $this->faker->randomFloat(2, 0.01),
            'wallet_sender_id'   => $this->faker->randomDigit(),
            'wallet_receiver_id' => $this->faker->randomDigit()
        ];

        $walletSender   = \Mockery::mock(Wallet::class);
        $walletReceiver = \Mockery::mock(Wallet::class);

        Carbon::setTestNow($this->faker->dateTime);
        $date = Carbon::now();

        $transferDtoExpected = new TransferDto($params['value'], $walletSender, $walletReceiver, $date);

        $this->validator->allows('setRules');
        $this->validator->allows('setMessages');
        $this->validator->allows('validate');

        $this->repository
            ->shouldReceive('create')
            ->with($params)
            ->andReturn($transferDtoExpected)
            ->once();

        $result = $this->service->create($params);

        $this->assertEquals($transferDtoExpected, $result);
    }

    /**
     * @test
     */
    public function shouldValidateToTransfer()
    {
        $rules = [
            'value'              => 'required|min:0.01',
            'wallet_sender_id'   => 'required|exists:wallets,id',
            'wallet_receiver_id' => 'required|exists:wallets,id|not_same_wallet',
        ];

        $messages = [
            'value.required'                     => trans('transfer.value.required'),
            'value.min'                          => trans('transfer.value.min'),
            'wallet_sender_id.required'          => trans('transfer.wallet_sender_id.required'),
            'wallet_sender_id.exists'            => trans('transfer.wallet_sender_id.exists'),
            'wallet_receiver_id.required'        => trans('transfer.wallet_sender_id.required'),
            'wallet_receiver_id.exists'          => trans('transfer.wallet_sender_id.exists'),
            'wallet_receiver_id.not_same_wallet' => trans('transfer.wallet_sender_id.exists'),
        ];

        $transferDto = \Mockery::mock(TransferDto::class);

        $this->repository
            ->shouldReceive('create')
            ->withAnyArgs()
            ->andReturn($transferDto)
            ->once();

        $params = [];

        $this->validator->shouldReceive('setRules')->with($rules)->once();
        $this->validator->shouldReceive('setMessages')->with($messages)->once();
        $this->validator->shouldReceive('validate')->with($params)->once();

        $this->service->create($params);
    }
}