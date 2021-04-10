<?php
namespace App\Presentation\Controllers;

use App\Domain\UseCases\CreateTransfer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Presentation\Contracts\Controller as ControllerContract;
use Illuminate\Http\Response;

class CreateTransferController extends Controller implements ControllerContract
{
    private CreateTransfer $service;

    public function __construct(CreateTransfer $service)
    {
        $this->service = $service;
    }

    public function handle(Request|null $request): JsonResponse
    {
        $result = $this->service->create($request->all());
        return new JsonResponse($result, Response::HTTP_CREATED);
    }
}