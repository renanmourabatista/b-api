<?php

namespace Database\Factories\Domain\Models;

use App\Domain\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition()
    {
        return [
            'cnpj'      => $this->faker->cnpj(false),
            'person_id' => null,
        ];
    }
}
