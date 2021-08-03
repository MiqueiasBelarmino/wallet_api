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
        User::create([
            'name' => 'Miqueias',
            'email' => 'miqueias@email.com',
            'password' => Hash::make('123456789')
        ]);

        for($i = 0; $i< 7; $i++)
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
