<?php

namespace Database\Factories\Domain\Models;

use App\Domain\Models\Person;
use \Faker\Provider\pt_BR\Person as PersonFaker;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonFactory extends Factory
{
    protected $model = Person::class;

    public function definition()
    {
        $this->faker->addProvider(new PersonFaker($this->faker));

        return [
            'name' => $this->faker->name,
            'cpf'  => $this->faker->cpf(false),
        ];
    }
}
