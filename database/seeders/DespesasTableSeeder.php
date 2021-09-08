<?php

namespace Database\Seeders;

use App\Models\Despesa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DespesasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 11; $i < 13; $i++){
            Despesa::create([
                'nome' => Str::random(10),
                'valor' => rand(0,200)/10,
                'data_vencimento' => date('Y-m-d', strtotime("+".$i." day", strtotime(date("Y-m-d")))),
                'user_id' => User::first()->id
            ]);
        }
        Despesa::create([
            'nome' => 'Aluguel',
            'valor' => 600,
            'data_vencimento' => date('Y-m-14'),
            'recorrencia' => env('RECORRENCIA_MENSAL'),
            'user_id' => User::first()->id
        ]);
        for($i = 11; $i < 20; $i++){
            Despesa::create([
                'nome' => Str::random(10),
                'valor' => rand(0,200)/10,
                'data_vencimento' => date('Y-m-d', strtotime("+".$i." day", strtotime(date("Y-m-d")))),
                'user_id' => rand(2, 6)
            ]);
        }
        Despesa::create([
            'nome' => 'dadssad',
            'valor' => 100,
            'data_vencimento' => date('Y-'.(date('m')+1).'-d'),
            'user_id' => User::first()->id
        ]);
    }
}
