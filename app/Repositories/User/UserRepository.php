<?php

namespace App\Repositories\User;

use App\Models\User;


class UserRepository implements UserRepositoryInterface
{
    public function create($data)
    {
        return User::create($data);
    }

    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

}
