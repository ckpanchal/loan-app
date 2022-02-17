<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
            ],
            [
                'name' => 'User',
                'slug' => 'user',
            ],
        ]; 
        if ($roles) {
            foreach ($roles as $role) {
                Role::updateOrCreate(
                    ['slug' => $role['slug']],
                    $role
                );
            }
        }
    }
}
