<?php

namespace Tests\Integration;

use App\Domain\Models\Transfer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizeTransfersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function shouldCompleteTransfers()
    {
        $numberOfTransfers = $this->faker->numberBetween(1, 10);

        Transfer::factory()->count($numberOfTransfers)->create(['status' => Transfer::STATUS_PENDING]);

        $this->artisan('authorize:transfers')->assertExitCode(0);
    }
}