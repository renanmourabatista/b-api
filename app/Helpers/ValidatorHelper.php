<?php

namespace App\Helpers;

use App\Data\Contracts\Validator;
use Illuminate\Support\Facades\Validator as LaravelValidator;

class ValidatorHelper implements Validator
{
    private array $messages;

    private array $rules;

    public function setRules(array $rules): void
    {
        $this->rules = $rules;
    }

    public function setMessages(array $messages): void
    {
        $this->rules = $messages;
    }

    public function validate(array $params): void
    {
        LaravelValidator::make($params, $this->rules, $this->messages)
            ->validate();
    }
}