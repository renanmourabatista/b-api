<?php

namespace Database\Factories\Domain\Models;

use App\Domain\Models\Company;
use Faker\Provider\pt_BR\Company as CompanyFaker;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition()
    {
        $this->faker->addProvider(new CompanyFaker($this->faker));

        return [
            'cnpj'      => $this->faker->cnpj(false),
            'person_id' => null,
        ];
    }
}
