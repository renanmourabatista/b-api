<?php

namespace Database\Factories\Domain\Models;

use App\Domain\Models\Transfer;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransferFactory extends Factory
{
    protected $model = Transfer::class;

    public function definition(): array
    {
        return [
            'value'                => $this->faker->randomFloat(2),
            'status'               => $this->faker->randomElement(
                [
                    Transfer::STATUS_NOT_AUTHORIZED,
                    Transfer::STATUS_PENDING,
                    Transfer::STATUS_AUTHORIZED
                ]
            ),
            'notification_date'    => $this->faker->dateTime(),
            'wallet_sender_id'     => null,
            'wallet_receiver_id'   => null,
            'transfer_reverted_id' => null,
        ];
    }
}
