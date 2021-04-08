<?php

namespace Database\Factories\Domain\Models;

use App\Domain\Models\Transfer;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransferFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transfer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
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
