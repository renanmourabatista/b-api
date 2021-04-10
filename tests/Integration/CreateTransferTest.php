<?php

namespace Tests\Integration;

use Illuminate\Http\Response;
use Tests\TestCase;

class CreateTransferTest extends TestCase
{
    public function shouldCreateATransfer()
    {
        $response = $this->post('/wallets/1/transfers');

        $response->assertStatus(Response::HTTP_CREATED);
    }
}