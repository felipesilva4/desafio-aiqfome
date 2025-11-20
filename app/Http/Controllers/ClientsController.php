<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ClientsController extends Controller
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }


    public function index()
    {
        //
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

    /**
     * O desafio não impoe um sistema de roles, como ADMIN, USER, etc. por exemplo
     * Logo o usuario so pode ver o seu proprio usuario
     */
    public function show(Request $request)
    {
        $user = auth('api')->user();

        return response()->json([
            'user' => $user,
        ]);
    }

    public function showAll(): JsonResponse
    {
        $users = $this->userRepository->getAllUsers();

        if (!$users) {
            return response()->json([
                'message' => 'Nenhum usuário encontrado',
            ], 404);
        }

        return response()->json([
            'users' => $users,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $user = auth('api')->user();

        
    }

    public function destroy(string $id)
    {
        //
    }
}
