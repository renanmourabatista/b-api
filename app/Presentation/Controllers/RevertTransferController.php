<?php
namespace App\Presentation\Controllers;

use App\Domain\UseCases\RevertTransfer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Presentation\Contracts\Controller as ControllerContract;
use Illuminate\Http\Response;

class RevertTransferController extends Controller implements ControllerContract
{
    private RevertTransfer $service;

    public function __construct(RevertTransfer $service)
    {
        $this->service = $service;
    }

    public function handle(Request $request): JsonResponse
    {
        $result = $this->service->revert((int)$request->id);
        return new JsonResponse($result, Response::HTTP_OK);
    }
}