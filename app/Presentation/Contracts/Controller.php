<?php

namespace App\Presentation\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface Controller
{
    public function handle(?Request $request): JsonResponse;
}