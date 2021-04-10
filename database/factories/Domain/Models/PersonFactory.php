<?php

namespace Database\Factories\Domain\Models;

use App\Domain\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonFactory extends Factory
{
    protected $model = Person::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'cpf'  => $this->faker->cpf(false),
        ];
    }
}
