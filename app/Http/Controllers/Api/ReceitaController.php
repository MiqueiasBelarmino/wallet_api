<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Receita;
use Illuminate\Http\Request;

class ReceitaController extends Controller
{
    public function index()
    {
        $receitas = Receita::where('user_id','=',auth()->user()->id)->get();
        return response($receitas, 200);
    }

    public function show($id)
    {
        if (Receita::where('id', $id)->exists()) {
            $receita = Receita::where('id', $id)->get();
            return response()->json($receita, 200);
        } else {
            return response()->json([
                "message" => "Receita não encontrada!"
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
            "message" => "Receita registrada!"
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
                "message" => "Receita atualizada!"
            ], 200);
        } else {
            return response()->json([
                "message" => "Receita não encontrada"
            ], 404);
        }
    }

    public function delete($id)
    {
        if (Receita::where('id', $id)->exists()) {

            $receita = Receita::find($id);
            $receita->delete();

            return response([
                "message" => "Receita removida!"
            ], 202);
        } else {
            return response()->json([
                "message" => "Receita não encontrada"
            ], 404);
        }
    }
}
