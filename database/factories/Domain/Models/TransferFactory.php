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
            'value'       => $this->faker->randomFloat(2),
            'date'        => $this->faker->dateTime,
            'sender_id'   => null,
            'receiver_id' => null
        ];
    }
}
