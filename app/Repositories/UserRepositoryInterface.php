<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function getUserByEmail(string $email): ?User;
    public function createUser(array $data): User;
    public function getAllUsers(): ?Collection;
    public function updateUser(string $id, array $data): User;
    public function deleteUser(string $id): ?User;
}
