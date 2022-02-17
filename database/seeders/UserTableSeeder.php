<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('secret'),
                'role_id' => 1
            ],
            [
                'name' => 'Normal User',
                'email' => 'user@example.com',
                'password' => Hash::make('secret'),
                'role_id' => 2
            ],
        ];
        foreach ($users as $user) {
            User::updateOrCreate([
                'email' => $user['email']
            ],$user);
        } 
    }
}
