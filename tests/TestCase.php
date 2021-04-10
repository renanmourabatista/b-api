<?php

namespace Tests;

use Faker\Provider\pt_BR\Company;
use Faker\Provider\pt_BR\Person;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, WithFaker;

    protected string $locale = 'pt_BR';

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker->addProvider(new Person($this->faker));
        $this->faker->addProvider(new Company($this->faker));
    }
}
