<?php

namespace App\Repositories\User;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Create new user
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        return User::create($data);
    }

    /**
     * Find user by email
     * @param $email
     * @return mixed
     */
    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

}
