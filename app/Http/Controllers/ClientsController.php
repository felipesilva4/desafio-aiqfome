<?php

namespace App\Http\Controllers;

use App\DTOs\ClientDTO;
use App\DTOs\ClientWithFavoritesDTO;
use App\Repositories\ClientsInterfaceRepository;
use App\Services\ValidateProductsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ClientsController extends Controller
{
    public function __construct(
        private readonly ClientsInterfaceRepository $clientsRepository,
        private readonly ValidateProductsService $validateProductsService
    ) {
    }

    #[OA\Post(
        path: '/api/clients',
        summary: 'Criar novo cliente',
        description: 'Cria um novo cliente no sistema',
        tags: ['Clientes'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'João Silva'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao@example.com'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Cliente criado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Usuário criado com sucesso'),
                        new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'João Silva'),
                                new OA\Property(property: 'email', type: 'string', example: 'joao@example.com'),
                            ]
                        ),
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
                response: 409,
                description: 'Email já cadastrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Este email já está cadastrado.'),
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
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients',
        ]);

        $user = $this->clientsRepository->createClient($validated);

        $clientDTO = new ClientDTO(
            id: $user->id,
            name: $user->name,
            email: $user->email,
        );

        return response()->json([
            'message' => 'Usuário criado com sucesso',
            'user' => $clientDTO->toArray(),
        ], 201);
    }

    #[OA\Get(
        path: '/api/clients',
        summary: 'Listar todos os clientes',
        description: 'Retorna uma lista com todos os clientes cadastrados',
        tags: ['Clientes'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de clientes',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'users',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'João Silva'),
                                    new OA\Property(property: 'email', type: 'string', example: 'joao@example.com'),
                                ]
                            )
                        ),
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
                description: 'Nenhum cliente encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Nenhum usuário encontrado'),
                    ]
                )
            ),
        ]
    )]
    public function showAll(): JsonResponse
    {
        $clients = $this->clientsRepository->getAllClients();

        if (!$clients) {
            return response()->json([
                'message' => 'Nenhum usuário encontrado',
            ], 404);
        }

        $clientsDTO = $clients->map(function ($client) {
            $clientDTO = new ClientDTO(
                id: $client->id,
                name: $client->name,
                email: $client->email,
            );
            return $clientDTO->toArray();
        });

        return response()->json([
            'users' => $clientsDTO,
        ]);
    }

    #[OA\Get(
        path: '/api/clients/{user_id}',
        summary: 'Buscar cliente por ID',
        description: 'Retorna os dados de um cliente específico incluindo seus produtos favoritos',
        tags: ['Clientes'],
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
                description: 'Dados do cliente com produtos favoritos',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'João Silva'),
                                new OA\Property(property: 'email', type: 'string', example: 'joao@example.com'),
                            ]
                        ),
                        new OA\Property(
                            property: 'favorite_products',
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
                        ),
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
                description: 'Cliente não encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Usuário não encontrado'),
                    ]
                )
            ),
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $client = $this->clientsRepository->getClient($id);

        if (!$client) {
            return response()->json([
                'message' => 'Usuário não encontrado',
            ], 404);
        }

        $favoriteProducts = $this->validateProductsService->showFavoriteProducts($id);

        $clientDTO = new ClientDTO(
            id: $client->id,
            name: $client->name,
            email: $client->email,
        );

        $clientWithFavoritesDTO = new ClientWithFavoritesDTO(
            client: $clientDTO,
            favoriteProducts: $favoriteProducts,
        );

        return response()->json($clientWithFavoritesDTO->toArray());
    }

    #[OA\Put(
        path: '/api/clients/{user_id}',
        summary: 'Atualizar cliente',
        description: 'Atualiza os dados de um cliente existente. Permite atualização parcial dos campos.',
        tags: ['Clientes'],
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
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'João Silva Atualizado'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao.novo@example.com'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cliente atualizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Usuário atualizado com sucesso'),
                        new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'João Silva Atualizado'),
                                new OA\Property(property: 'email', type: 'string', example: 'joao.novo@example.com'),
                            ]
                        ),
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
                description: 'Cliente não encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Usuário não encontrado'),
                    ]
                )
            ),
            new OA\Response(
                response: 409,
                description: 'Email já cadastrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Este email já está cadastrado.'),
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
    #[OA\Patch(
        path: '/api/clients/{user_id}',
        summary: 'Atualizar cliente (PATCH)',
        description: 'Atualiza os dados de um cliente existente. Permite atualização parcial dos campos.',
        tags: ['Clientes'],
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
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'João Silva Atualizado'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao.novo@example.com'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cliente atualizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Usuário atualizado com sucesso'),
                        new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'João Silva Atualizado'),
                                new OA\Property(property: 'email', type: 'string', example: 'joao.novo@example.com'),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function update(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:clients,email,' . $id,
        ]);

        if (empty($validated)) {
            return response()->json([
                'message' => 'Nenhum campo fornecido para atualização',
            ], 422);
        }

        $updatedUser = $this->clientsRepository->updateClient($id, $validated);

        if (!$updatedUser) {
            return response()->json([
                'message' => 'Usuário não encontrado',
            ], 404);
        }

        $clientDTO = new ClientDTO(
            id: $updatedUser->id,
            name: $updatedUser->name,
            email: $updatedUser->email,
        );

        return response()->json([
            'message' => 'Usuário atualizado com sucesso',
            'user' => $clientDTO->toArray(),
        ]);
    }

    #[OA\Delete(
        path: '/api/clients/{user_id}',
        summary: 'Deletar cliente',
        description: 'Remove um cliente do sistema',
        tags: ['Clientes'],
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
                description: 'Cliente deletado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Usuário deletado com sucesso'),
                        new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'João Silva'),
                                new OA\Property(property: 'email', type: 'string', example: 'joao@example.com'),
                            ]
                        ),
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
                description: 'Cliente não encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Usuário não encontrado'),
                    ]
                )
            ),
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        $deletedUser = $this->clientsRepository->deleteClient($id);

        if (!$deletedUser) {
            return response()->json([
                'message' => 'Usuário não encontrado',
            ], 404);
        }

        $clientDTO = new ClientDTO(
            id: $deletedUser->id,
            name: $deletedUser->name,
            email: $deletedUser->email,
        );

        return response()->json([
            'message' => 'Usuário deletado com sucesso',
            'user' => $clientDTO->toArray(),
        ]);
    }
}
