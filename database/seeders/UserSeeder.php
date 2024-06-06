<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::create([
            'name'=>'sadmin',
            'email'=>'sadmin@gmail.com',
            'photo'=>'user.jpg',
            'password'=>Hash::make('sadmin123')
        ]);

        $users -> assignRole([1]);

        $users = User::create([
            'name'=>'admin',
            'email'=>'admin@gmail.com',
            'photo'=>'user.jpg',
            'password'=>Hash::make('admin123')
        ]);

        $users -> assignRole([2]);
    }
}
