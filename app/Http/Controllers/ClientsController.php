<?php

namespace App\Http\Controllers;

use App\Repositories\ClientsInterfaceRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientsController extends Controller
{
    public function __construct(
        private readonly ClientsInterfaceRepository $clientsRepository
    ) {
    }

    
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients',
            'password' => 'required|string|min:8',
        ]);

        $user = $this->clientsRepository->createClient($validated);

        return response()->json([
            'message' => 'Usuário criado com sucesso',
            'user' => $user,
        ], 201);
    }

    public function showAll(Request $request): JsonResponse
    {
        $clients = $this->clientsRepository->getAllClients();

        if (!$clients) {
            return response()->json([
                'message' => 'Nenhum usuário encontrado',
            ], 404);
        }

        return response()->json([
            'users' => $clients,
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $client = $this->clientsRepository->getClient($id);

        if (!$client) {
            return response()->json([
                'message' => 'Usuário não encontrado',
            ], 404);
        }

        return response()->json([
            'user' => $client,
        ]);
    }
    
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

        return response()->json([
            'message' => 'Usuário atualizado com sucesso',
            'user' => $updatedUser,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $deletedUser = $this->clientsRepository->deleteClient($id);

        if (!$deletedUser) {
            return response()->json([
                'message' => 'Usuário não encontrado',
            ], 404);
        }

        return response()->json([
            'message' => 'Usuário deletado com sucesso',
            'user' => $deletedUser,
        ]);
    }
}
