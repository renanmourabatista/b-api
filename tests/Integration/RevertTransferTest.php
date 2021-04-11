<?php

namespace Tests\Integration;

use App\Domain\Models\Person;
use App\Domain\Models\Transfer;
use App\Domain\Models\User;
use App\Domain\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\Response;
use Tests\TestCase;

class RevertTransferTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    /**
     * @test
     */
    public function shouldRevertATransfer()
    {
        $personPayer = Person::factory()->create();
        User::factory()->create(['person_id' => $personPayer->id]);
        $walletPayer = Wallet::factory()->create(['person_id' => $personPayer->id]);

        $personPayee = Person::factory()->create();
        $userPayee = User::factory()->create(['person_id' => $personPayee->id]);
        $walletPayee = Wallet::factory()->create(['person_id' => $personPayee->id]);

        $valueToTransfer = $walletPayee->amount;

        $transfer = Transfer::factory()->create(
            [
                'wallet_sender_id'   => $walletPayer->id,
                'wallet_receiver_id' => $walletPayee->id,
                'status'             => Transfer::STATUS_AUTHORIZED,
                'value'              => $valueToTransfer
            ]
        );

        $this->actingAs($userPayee);

        $response = $this->put(
            '/api/wallets/transfers/'.$transfer->id.'/revert',
            ['Accept' => 'application/json']
        );

        $response->assertStatus(Response::HTTP_OK);
    }
}