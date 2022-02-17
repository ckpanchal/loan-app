<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Repository associate with this service
     *
     * @var object
     */
    protected $userRepo;

    /**
     * Initialize user service
     *
     * @param UserRepository $userRepo
     */
    public function __construct(UserRepository $userRepo) {
        $this->userRepo = $userRepo;
    }

    /**
     * Handle operation to get user data by id
     *
     * @param integer $id
     * @return void
     */
    public function findById($id) {
        $user = $this->userRepo->findById($id);
        return $user ? $user : FALSE;
    }

    /**
     * Handle operation to store user data
     *
     * @param array $data
     * @return void
     */
    public function create($data) {
        if (isset($data['password_confirmation'])) {
            unset($data['password_confirmation']);
        }
        $data['password'] = Hash::make($data['password']);
        $user = $this->userRepo->create($data);
        return $user ? $user : FALSE;
    }
}