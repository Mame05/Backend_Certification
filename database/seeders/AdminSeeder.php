<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un administrateur
        $users = [
            ['email' => 'admin@example.com',
            'password' => bcrypt('pass1secret'),
            'role_id' => 1,
        ],
    ];
    foreach ($users as $userData) {
        $user = User::create($userData);

         // Ajouter l'utilisateur dans la table admins
         Admin::create([
            'user_id' => $user->id,
        ]);
    }
    }
}
