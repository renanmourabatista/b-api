<?php

namespace Database\Factories\Domain\Models;

use App\Domain\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    public function definition()
    {
        return [
            'amount'    => $this->faker->randomFloat(2, 1, 999),
            'person_id' => null
        ];
    }
}
