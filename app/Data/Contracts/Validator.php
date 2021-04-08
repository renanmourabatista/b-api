<?php
namespace App\Data\Contracts;

interface Validator
{
    public function setRules(array $rules): void;

    public function setMessages(array $messages): void;

    public function validate(array $params): void;
}