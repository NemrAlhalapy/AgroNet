<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Admin extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user=User::create([
            'name'=>'Admin',
            'email'=>'adminal@gmail.com',
            'password' => bcrypt('0000000000'),
            'number_phone'=>0,
            'age'=>0,
            'role'=>'farmer',
            'status'=>0,
        ]);
       
    }
}
