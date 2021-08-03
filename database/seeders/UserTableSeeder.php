<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0; $i< 5; $i++)
        {
            $str = Str::random(10);
            User::create([
                'name' => ''.$str,
                'email' => $str.'@email.com',
                'password' => Hash::make('123456789')
            ]);
        }
    }
}
