<?php

namespace Tests\Integration;

use App\Domain\Models\Company;
use App\Domain\Models\Person;
use App\Domain\Models\Transfer;
use App\Domain\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotfyTransfersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function shouldNotifyTransfers()
    {
        $numberOfTransfers = $this->faker->numberBetween(1, 10);

        $personPayee = Person::factory()->create();
        Company::factory()->create(['person_id' => $personPayee->id]);
        $walletPayee = Wallet::factory()->create(['person_id' => $personPayee->id]);

        Transfer::factory()->count($numberOfTransfers)->create(
            [
                'status'             => Transfer::STATUS_AUTHORIZED,
                'wallet_receiver_id' => $walletPayee->id
            ]
        );

        $this->artisan('notify:transfers')->assertExitCode(0);
    }
}