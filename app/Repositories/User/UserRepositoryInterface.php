<?php

namespace App\Repositories\User;

interface UserRepositoryInterface
{

    /**
     * Create new user
     * @param $data
     * @return mixed
     */
    public function create($data);

    /**
     * Find user by email
     * @param $email
     * @return mixed
     */
    public function findByEmail($email);
}
