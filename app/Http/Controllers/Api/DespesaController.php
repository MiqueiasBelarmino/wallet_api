<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Despesa;
use Illuminate\Http\Request;

class DespesaController extends Controller
{
    public function index()
    {
        $despesas = Despesa::where('user_id','=',auth()->user()->id)->get();
        return response($despesas, 200);
    }

    public function show($id)
    {
        if (Despesa::where('id', $id)->exists()) {
            $despesa = Despesa::where('id', $id)->get();
            return response()->json($despesa, 200);
        } else {
            return response()->json([
                "message" => "Despesa não encontrada!"
            ], 404);
        }
    }

    public function create(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
            'valor' => 'required|numeric',
            'data_vencimento' => 'required|date'
        ]);

        $despesa = new Despesa;
        $despesa->nome = $request->nome;
        $despesa->valor = $request->valor;
        $despesa->data_vencimento = $request->data_vencimento;
        $despesa->user_id = auth()->user()->id;
        $despesa->save();

        return response()->json([
            "message" => "Despesa registrada!"
        ], 201);
    }

    public function update(Request $request, $id)
    {
        if (Despesa::where('id', $id)->exists()) {

            $despesa = Despesa::find($id);
            $despesa->nome = is_null($request->nome) ? $despesa->nome : $request->nome;
            $despesa->valor = is_null($request->valor) ? $despesa->valor : $request->valor;
            $despesa->data_vencimento = is_null($request->data_vencimento) ? $despesa->data_vencimento : $request->data_vencimento;
            $despesa->save();

            return response()->json([
                "message" => "Despesa atualizada!"
            ], 200);
        } else {
            return response()->json([
                "message" => "Despesa não encontrada"
            ], 404);
        }
    }

    public function delete($id)
    {
        if (Despesa::where('id', $id)->exists()) {

            $despesa = Despesa::find($id);
            $despesa->delete();

            return response([
                "message" => "Despesa removida!"
            ], 202);
        } else {
            return response()->json([
                "message" => "Despesa não encontrada"
            ], 404);
        }
    }
}
