<?php

namespace Database\Seeders;

use App\Models\Despesa;
use App\Models\Receita;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReceitasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 11; $i < 13; $i++){
            Receita::create([
                'nome' => Str::random(10),
                'valor' => rand(0,200)/10,
                'data_entrada' => date('Y-m-d', strtotime("+".$i." day", strtotime(date("Y-m-d")))),
                'user_id' => User::first()->id
            ]);
        }
        for($i = 11; $i < 20; $i++){
            Receita::create([
                'nome' => Str::random(10),
                'valor' => rand(0,200)/10,
                'data_entrada' => date('Y-m-d', strtotime("+".$i." day", strtotime(date("Y-m-d")))),
                'user_id' => rand(2, 6)
            ]);
        }
    }
}
