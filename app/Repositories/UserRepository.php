<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function createUser(array $data): User
    {
        return User::create($data);
    }

    /*
    * Como separei usuario do cliente, sendo usuario algo como usuario do sistema
    * Então aqui pode retornar todos os usuários
    */
    public function getAllUsers(): ?Collection
    {
        return User::all();
    }

    /*
    * Como pega algo de um usuario já logado, impossivel retornar null
    */
    public function updateUser(string $id, array $data): User
    {
        $user = User::find($id);

        $user->update($data);

        return $user;
    }

    public function deleteUser(string $id): ?User
    {
        $user = User::find($id);

        if (!$user) {
            return null;
        }

        $user->delete();

        return $user;
    }
}
