<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Receita;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ReceitaController extends Controller
{
    public function index(Request $request)
    {
        $receitas = [];
        if (isset($request->vencimento) && !empty($request->vencimento)) {
            $hoje = strtotime(date('Y-m'));
            $requestDate = strtotime(date('Y-m', strtotime($request->vencimento)));
            if ($requestDate > $hoje) {
                $receitas = Receita::where('user_id', '=', auth()->user()->id)
                    ->whereYear('data_entrada', '>=', date('Y'))
                    ->whereMonth('data_entrada', '>=', date('m'))
                    ->whereYear('data_entrada', '<=', date('Y', strtotime($request->vencimento)))
                    ->whereMonth('data_entrada', '<=', date('m', strtotime($request->vencimento)))->get();

                $inicial = $hoje;
                $final = $requestDate;
            } else if ($requestDate < $hoje) {
                $receitas = Receita::where('user_id', '=', auth()->user()->id)
                    ->whereYear('data_entrada', '>=', date('Y', strtotime($request->vencimento)))
                    ->whereMonth('data_entrada', '>=', date('m', strtotime($request->vencimento)))
                    ->whereYear('data_entrada', '<=', date('Y'))
                    ->whereMonth('data_entrada', '<=', date('m'))->get();

                $inicial = $requestDate;
                $final = $hoje;
            } else {
                $receitas = Receita::where('user_id', '=', auth()->user()->id)
                    ->whereYear('data_entrada', '=', date('Y', strtotime($request->vencimento)))
                    ->whereMonth('data_entrada', '=', date('m', strtotime($request->vencimento)))->get();
                $inicial = $requestDate;
                $final = $requestDate;
            }
            $d_array = new Collection();
            foreach ($receitas as $d) {
                if (intval($d->recorrencia) == intval(env('RECORRENCIA_MENSAL'))) {
                    $max = month_count(date('Y-m', $inicial), date('Y-m', $final));
                    for ($i = 0; $i < $max; $i++) {
                        $d_array->push($d);
                    }
                }
            }
            foreach ($d_array as $da) {
                $receitas->push($da);
            }
        } else {
            $receitas = Receita::where('user_id', '=', auth()->user()->id)->get();
        }

        return response($receitas, 200);
    }

    public function show($id)
    {
        if (Receita::where('id', $id)->exists()) {
            $receita = Receita::where('id', $id)->get();
            return response()->json($receita, 200);
        } else {
            return response()->json([
                "message" => "Receita não encontrada!",
                "status" => env('CODE_NOT_FOUND')
            ], 404);
        }
    }

    public function create(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
            'valor' => 'required|numeric',
            'data_entrada' => 'required|date'
        ]);

        $receita = new Receita;
        $receita->nome = $request->nome;
        $receita->valor = $request->valor;
        $receita->data_entrada = $request->data_entrada;
        $receita->user_id = auth()->user()->id;
        $receita->save();

        return response()->json([
            "message" => "Receita registrada!",
            "status" => env('CODE_SUCCESS')
        ], 201);
    }

    public function update(Request $request, $id)
    {
        if (Receita::where('id', $id)->exists()) {

            $receita = Receita::find($id);
            $receita->nome = is_null($request->nome) ? $receita->nome : $request->nome;
            $receita->valor = is_null($request->valor) ? $receita->valor : $request->valor;
            $receita->data_entrada = is_null($request->data_entrada) ? $receita->data_entrada : $request->data_entrada;
            $receita->save();

            return response()->json([
                "message" => "Receita atualizada!",
                "status" => env('CODE_SUCCESS')
            ], 200);
        } else {
            return response()->json([
                "message" => "Receita não encontrada",
                "status" => env('CODE_NOT_FOUND')
            ], 404);
        }
    }

    public function delete($id)
    {
        if (Receita::where('id', $id)->exists()) {

            $receita = Receita::find($id);
            $receita->delete();

            return response([
                "message" => "Receita removida!",
                "status" => env('CODE_SUCCESS')
            ], 202);
        } else {
            return response()->json([
                "message" => "Receita não encontrada",
                "status" => env('CODE_NOT_FOUND')
            ], 404);
        }
    }
}
