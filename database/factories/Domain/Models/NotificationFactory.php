<?php

namespace Database\Factories\Domain\Models;

use App\Domain\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition()
    {
        return [
            'cnpj'  => $this->faker->cnpj(false),
        ];
    }
}
