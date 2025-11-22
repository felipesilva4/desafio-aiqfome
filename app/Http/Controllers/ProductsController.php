<?php

namespace App\Http\Controllers;

use App\DTOs\FavoriteProductDTO;
use App\Repositories\ClientsInterfaceRepository;
use App\Services\ValidateProductsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProductsController extends Controller
{
    public function __construct(
        private readonly ValidateProductsService $validateProductsService,
        private readonly ClientsInterfaceRepository $clientsRepository
    ) {
    }

    #[OA\Post(
        path: '/api/clients/{user_id}/favorites',
        summary: 'Adicionar produto favorito',
        description: 'Adiciona um produto à lista de favoritos de um cliente',
        tags: ['Produtos Favoritos'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'user_id',
                in: 'path',
                required: true,
                description: 'ID do cliente',
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['product_id'],
                properties: [
                    new OA\Property(property: 'product_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Produto favoritado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Produto favoritado com sucesso'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Não autenticado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Não autenticado.'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Produto não encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Produto não encontrado'),
                    ]
                )
            ),
            new OA\Response(
                response: 409,
                description: 'Produto já favoritado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Este produto já está nos favoritos.'),
                        new OA\Property(property: 'error', type: 'string', example: 'product_already_favorited'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Dados inválidos',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'errors', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function storeFavoriteProduct(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'integer|required',
        ]);

        //Optei pelo service para deixar a controller com apenas a responsabilidade
        //de validar os dados e chamar o repository para salvar o produto favorito
        $products = $this->validateProductsService->validateProducts(
            $validated['product_id'],
             $request->user_id
        );

        if (!$products) {
            return response()->json([
                'message' => 'Produto não encontrado',
            ], 404);
        }

        return response()->json([
            'message' => 'Produto favoritado com sucesso',
        ]);
    }

    #[OA\Get(
        path: '/api/clients/{user_id}/favorites',
        summary: 'Listar produtos favoritos',
        description: 'Retorna a lista de produtos favoritos de um cliente',
        tags: ['Produtos Favoritos'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'user_id',
                in: 'path',
                required: true,
                description: 'ID do cliente',
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de produtos favoritos',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'titulo', type: 'string', example: 'Produto Exemplo'),
                            new OA\Property(property: 'imagem', type: 'string', example: 'https://example.com/image.jpg'),
                            new OA\Property(property: 'preco', type: 'number', format: 'float', example: 99.90),
                            new OA\Property(property: 'descricao', type: 'string', example: 'Descrição do produto'),
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Não autenticado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Não autenticado.'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Cliente não encontrado',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items()
                )
            ),
        ]
    )]
    public function showFavoriteProducts(Request $request): JsonResponse
    {
        $client = $this->clientsRepository->getClient($request->user_id);

        if (!$client) {
            return response()->json([], 404);   
        }

        $products = $this->validateProductsService->showFavoriteProducts($request->user_id);
        
        $productsDTO = [];
        foreach ($products as $product) {
            $productsDTO[] = (new FavoriteProductDTO((array) $product))->toArray();
        }

        return response()->json($productsDTO ?? []);
    }
}

