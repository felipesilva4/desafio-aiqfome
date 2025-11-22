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
}
