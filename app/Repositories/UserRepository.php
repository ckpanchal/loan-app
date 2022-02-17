<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * Handle logic to get data by id 
     *
     * @param integer $id
     * @return void
     */
    public function findById($id) {
        return User::find($id);
    }

    /**
     * Handle logic to store user data
     *
     * @param array $data
     * @return void
     */
    public function create($data) {
        return User::create($data);
    }
}