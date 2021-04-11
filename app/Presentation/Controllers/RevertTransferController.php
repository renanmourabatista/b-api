<?php
namespace App\Presentation\Controllers;

use App\Domain\UseCases\RevertTransfer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Presentation\Contracts\Controller as ControllerContract;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RevertTransferController extends Controller implements ControllerContract
{
    private RevertTransfer $service;

    public function __construct(RevertTransfer $service)
    {
        $this->service = $service;
    }

    /**
     * @api {post} /wallets/transfers/:id/revert Reverter uma transferencia
     * @apiName handle
     * @apiGroup Reverter Transferência
     *
     * @apiParam {Number} id ID transferencia a ser revertida.
     *
     * @apiSuccess (200) {Number} id ID da transferência.
     * @apiSuccess (200) {float} value Valor transferido.
     * @apiSuccess (200) {Number} wallet_payee_id ID da carteira do recebedor.
     * @apiSuccess (200) {Number} wallet_payer_id ID da carteira do pagador.
     * @apiSuccess (200) {Number} status 0 - Pendente, 1 - Autorizada, 2 - Não autorizada.
     * @apiSuccess (200) {Datetime} updated_at Ultima atualização da transferência.
     * @apiSuccess (200) {Datetime} created_at Data de criação da transferência.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *          "value":774.67,
     *          "wallet_payee_id":2,
     *          "wallet_payer_id":1,
     *          "status":0,
     *          "updated_at":"2021-04-11T12:44:48.000000Z",
     *          "created_at":"2021-04-11T12:44:48.000000Z",
     *          "id":1
     *      }
     *
     * @apiError AccessDenied Erro de violação de regras.
     * @apiErrorExample Error-Response:
     *      HTTP/1.1 403 Access Denied
     *      {
     *          "message": "Insufficient funds"
     *      }
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $result = $this->service->revert((int)$request->id);

            return new JsonResponse($result, Response::HTTP_OK);
        } catch (HttpException $exception) {
            return $this->errorResponse($exception);
        }
    }
}