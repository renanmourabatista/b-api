<?php
namespace Tests\Unit\Helpers;

use App\Helpers\ValidatorHelper;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ValidatorHelperTest extends TestCase
{
    /**
     * @test
     */
    public function shouldValidateWithLaravelValidator()
    {
        $this->createApplication();

        $helper = new ValidatorHelper();
        $field = $this->faker->word;
        $value = $this->faker->word;
        $params = [$field => $value];

        $validator = \Mockery::mock();

        $validator
            ->shouldReceive('validate')
            ->withNoArgs()
            ->andReturnNull()
            ->once();

        Validator::shouldReceive('make')
            ->with($params, [], [])
            ->andReturn($validator)
            ->once();

        $helper->validate($params);
    }
}