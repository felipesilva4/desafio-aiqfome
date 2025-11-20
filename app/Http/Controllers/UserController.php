<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = $this->userRepository->createUser($validated);

        return response()->json([
            'message' => 'Usuário criado com sucesso',
            'user' => $user,
        ], 201);
    }

    public function show(Request $request): JsonResponse
    {
        $users = $this->userRepository->getAllUsers();

        return response()->json([
            'users' => $users,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'nome' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
        ]);

        if (empty($validated)) {
            return response()->json([
                'message' => 'Nenhum campo fornecido para atualização',
            ], 422);
        }

        $updatedUser = $this->userRepository->updateUser($id, $validated);

        return response()->json([
            'message' => 'Usuário atualizado com sucesso',
            'user' => $updatedUser,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $deletedUser = $this->userRepository->deleteUser($id);

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
