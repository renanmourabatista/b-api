<?php

namespace Tests\Integration;

use App\Domain\Models\Person;
use App\Domain\Models\User;
use App\Domain\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\Response;
use Tests\TestCase;

class CreateTransferTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    /**
     * @test
     */
    public function shouldCreateATransfer()
    {
        $personPayer = Person::factory()->create();
        $userPayer   = User::factory()->create(['person_id' => $personPayer->id]);
        $walletPayer = Wallet::factory()->create(['person_id' => $personPayer->id]);

        $valueToTransfer = $walletPayer->amount;

        $personPayee = Person::factory()->create();
        User::factory()->create(['person_id' => $personPayee->id]);
        $walletPayee = Wallet::factory()->create(['person_id' => $personPayee->id]);

        $this->actingAs($userPayer);

        $data = [
            'value'           => $valueToTransfer,
            'wallet_payee_id' => $walletPayee->id
        ];

        $response = $this->post('/api/wallets/transfers', $data, ['Accept' => 'application/json']);

        $response->assertStatus(Response::HTTP_CREATED);
    }
}