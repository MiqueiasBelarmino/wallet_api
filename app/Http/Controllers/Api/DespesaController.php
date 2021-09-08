<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Despesa;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class DespesaController extends Controller
{
    public function index(Request $request)
    {
        $despesas = [];
        $inicial = '';
        $final = '';
        if (isset($request->vencimento) && !empty($request->vencimento)) {
            $hoje = strtotime(date('Y-m'));
            $requestDate = strtotime(date('Y-m', strtotime($request->vencimento)));
            if ($requestDate > $hoje) {
                $despesas = Despesa::where('user_id', '=', auth()->user()->id)
                    ->whereYear('data_vencimento', '>=', date('Y'))
                    ->whereMonth('data_vencimento', '>=', date('m'))
                    ->whereYear('data_vencimento', '<=', date('Y', strtotime($request->vencimento)))
                    ->whereMonth('data_vencimento', '<=', date('m', strtotime($request->vencimento)))->get();

                $inicial = $hoje;
                $final = $requestDate;
            } else if ($requestDate < $hoje) {
                $despesas = Despesa::where('user_id', '=', auth()->user()->id)
                    ->whereYear('data_vencimento', '>=', date('Y', strtotime($request->vencimento)))
                    ->whereMonth('data_vencimento', '>=', date('m', strtotime($request->vencimento)))
                    ->whereYear('data_vencimento', '<=', date('Y'))
                    ->whereMonth('data_vencimento', '<=', date('m'))->get();
                $inicial = $requestDate;
                $final = $hoje;
            } else {
                $despesas = Despesa::where('user_id', '=', auth()->user()->id)
                    ->whereYear('data_vencimento', '=', date('Y', strtotime($request->vencimento)))
                    ->whereMonth('data_vencimento', '=', date('m', strtotime($request->vencimento)))->get();
                $inicial = $requestDate;
                $final = $requestDate;
            }
            $d_array = new Collection;
            foreach ($despesas as $d) {
                if (intval($d->recorrencia) == intval(env('RECORRENCIA_MENSAL'))) {
                    $max = month_count(date('Y-m', $inicial), date('Y-m', $final));
                    for ($i = 0; $i < $max; $i++) {
                        $d_array->push($d);
                    }
                }
            }
            foreach ($d_array as $da) {
                $despesas->push($da);
            }
        } else {
            $despesas = Despesa::where('user_id', '=', auth()->user()->id)->get();
        }

        return response($d_array, 200);
    }

    public function show($id)
    {
        if (Despesa::where('id', $id)->exists()) {
            $despesa = Despesa::where('id', $id)->get();
            return response()->json($despesa, 200);
        } else {
            return response()->json([
                "message" => "Despesa não encontrada!",
                "status" => env('CODE_NOT_FOUND')
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
            "message" => "Despesa registrada!",
            "status" => env('CODE_SUCCESS')
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
                "message" => "Despesa atualizada!",
                "status" => env('CODE_SUCCESS')
            ], 200);
        } else {
            return response()->json([
                "message" => "Despesa não encontrada",
                "status" => env('CODE_NOT_FOUND')
            ], 404);
        }
    }

    public function delete($id)
    {
        if (Despesa::where('id', $id)->exists()) {

            $despesa = Despesa::find($id);
            $despesa->delete();

            return response([
                "message" => "Despesa removida!",
                "status" => env('CODE_SUCCESS')
            ], 202);
        } else {
            return response()->json([
                "message" => "Despesa não encontrada",
                "status" => env('CODE_NOT_FOUND')
            ], 404);
        }
    }
}
