<?php
namespace App\Presentation\Controllers;

use App\Domain\UseCases\CreateTransfer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Presentation\Contracts\Controller as ControllerContract;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CreateTransferController extends Controller implements ControllerContract
{
    private CreateTransfer $service;

    public function __construct(CreateTransfer $service)
    {
        $this->service = $service;
    }

    /**
     * @api {post} /wallets/transfers Criar uma transferencia
     * @apiName handle
     * @apiGroup Criar Transferência
     *
     * @apiParam {float} value Valor a ser transferido.
     * @apiParam {Number} wallet_payee_id ID da carteira do recebedor.
     *
     * @apiSuccess (201) {Number} id ID da transferência.
     * @apiSuccess (201) {float} value Valor transferido.
     * @apiSuccess (201) {Number} wallet_payee_id ID da carteira do recebedor.
     * @apiSuccess (201) {Number} wallet_payer_id ID da carteira do pagador.
     * @apiSuccess (201) {Number} status 0 - Pendente, 1 - Autorizada, 2 - Não autorizada.
     * @apiSuccess (201) {Datetime} updated_at Ultima atualização da transferência.
     * @apiSuccess (201) {Datetime} created_at Data de criação da transferência.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 201 CREATED
     *     {
     *          "value":774.67,
     *          "wallet_payee_id":2,
     *          "wallet_payer_id":1,
     *          "status":0,
     *          "updated_at":"2021-04-11T12:44:48.000000Z",
     *          "created_at":"2021-04-11T12:44:48.000000Z",
     *          "id":1
     *      }
     * @apiError UnprocessableEntity Erro de envio de dados.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 422 Unprocessable Entity
     *     {
     *      "message":"The given data was invalid.",
     *      "errors": {
     *              "value":[
     *                  "Value is required"
     *              ]
     *          }
     *      }
     *
     * @apiError AccessDenied Erro de violação de regras.
     * @apiErrorExample Error-Response:
     *      HTTP/1.1 403 Access Denied
     *      {
     *          "message":"Insufficient funds"
     *      }
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $result = $this->service->create($request->all());

            return new JsonResponse($result, Response::HTTP_CREATED);
        } catch (HttpException $exception) {
            return $this->errorResponse($exception);
        }
    }
}