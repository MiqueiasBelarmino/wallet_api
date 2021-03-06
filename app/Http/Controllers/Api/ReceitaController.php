<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Receita;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ReceitaController extends Controller
{
    // public function index(Request $request)
    // {
    //     $receitas = [];
    //     $inicial = '';
    //     $final = '';
    //     if (isset($request->vencimento) && !empty($request->vencimento)) {
    //         $hoje = strtotime(date('Y-m'));
    //         $requestDate = strtotime(date('Y-m', strtotime($request->vencimento)));
    //         if ($requestDate > $hoje) {
    //             $receitas = Receita::where('user_id', '=', auth()->user()->id)
    //                 ->whereYear('data_entrada', '>=', date('Y'))
    //                 ->whereMonth('data_entrada', '>=', date('m'))
    //                 ->whereYear('data_entrada', '<=', date('Y', strtotime($request->vencimento)))
    //                 ->whereMonth('data_entrada', '<=', date('m', strtotime($request->vencimento)))->get();

    //             $inicial = $hoje;
    //             $final = $requestDate;
    //         } else if ($requestDate < $hoje) {
    //             $receitas = Receita::where('user_id', '=', auth()->user()->id)
    //                 ->whereYear('data_entrada', '>=', date('Y', strtotime($request->vencimento)))
    //                 ->whereMonth('data_entrada', '>=', date('m', strtotime($request->vencimento)))
    //                 ->whereYear('data_entrada', '<=', date('Y'))
    //                 ->whereMonth('data_entrada', '<=', date('m'))->get();

    //             $inicial = $requestDate;
    //             $final = $hoje;
    //         } else {
    //             $receitas = Receita::where('user_id', '=', auth()->user()->id)
    //                 ->whereYear('data_entrada', '=', date('Y', strtotime($request->vencimento)))
    //                 ->whereMonth('data_entrada', '=', date('m', strtotime($request->vencimento)))->get();
    //             $inicial = $requestDate;
    //             $final = $requestDate;
    //         }
    //         $receitas_recorrentes = new Collection();
    //         foreach ($receitas as $r) {
    //             if (intval($r->recorrencia) == intval(env('RECORRENCIA_MENSAL'))) {
    //                 $max = month_count(date('Y-m', $inicial), date('Y-m', $final));
    //                 for ($i = 0; $i < $max; $i++) {
    //                     $receitas_recorrentes->push($r);
    //                 }
    //             }
    //         }
    //         foreach ($receitas_recorrentes as $da) {
    //             $receitas->push($da);
    //         }
    //     } else {
    //         $receitas = Receita::where('user_id', '=', auth()->user()->id)->get();
    //     }

    //     return response([
    //         'receitas'=> $receitas,
    //         'status'=> true
    //     ], 200);
    // }

    public function index(Request $request)
    {
        $receitas = [];
        if (isset($request->vencimento) && !empty($request->vencimento)) {
            $hoje = strtotime(date('Y-m'));
            $requestDate = strtotime(date('Y-m', strtotime($request->vencimento)));

            $receitas = Receita::where('user_id', '=', auth()->user()->id)
                ->whereYear('data_entrada', '<=', date('Y', strtotime($request->vencimento)))
                ->whereMonth('data_entrada', '<=', date('m', strtotime($request->vencimento)))->get();
        } else {
            $receitas = Receita::where('user_id', '=', auth()->user()->id)
                ->whereYear('data_entrada', '<=', date('Y'))
                ->whereMonth('data_entrada', '<=', date('m'))->get();
            $requestDate = strtotime(date('Y-m'));
        }
        $receitas_recorrentes = new Collection();
        foreach ($receitas as $r) {
            if (intval($r->recorrencia) == intval(env('RECORRENCIA_MENSAL'))) {
                $max = month_count(date('Y-m', strtotime($r->data_entrada)), date('Y-m', $requestDate));
                for ($i = 0; $i < $max; $i++) {
                    $receitas_recorrentes->push($r);
                }
            }
        }
        foreach ($receitas_recorrentes as $rr) {
            $receitas->push($rr);
        }
        return response([
            'receitas' => $receitas,
            'status' => true
        ], 200);
    }

    public function show($id)
    {
        if (Receita::where('id', $id)->exists()) {
            $receita = Receita::where('id', $id)->get();
            return response()->json($receita, 200);
        } else {
            return response()->json([
                "message" => "Receita n??o encontrada!",
                'status' => false
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
        $receita->recorrencia = $request->recorrencia;
        $receita->data_entrada = $request->data_entrada;
        $receita->user_id = auth()->user()->id;
        $receita->save();

        return response()->json([
            "message" => "Receita registrada!",
            'status' => true
        ], 201);
    }

    public function update(Request $request, $id)
    {
        if (Receita::where('id', $id)->exists()) {

            $receita = Receita::find($id);
            $receita->nome = is_null($request->nome) ? $receita->nome : $request->nome;
            $receita->valor = is_null($request->valor) ? $receita->valor : $request->valor;
            $receita->recorrencia = is_null($request->recorrencia) ? $receita->recorrencia : $request->recorrencia;
            $receita->data_entrada = is_null($request->data_entrada) ? $receita->data_entrada : $request->data_entrada;
            $receita->save();

            return response()->json([
                "message" => "Receita atualizada!",
                'status' => true
            ], 200);
        } else {
            return response()->json([
                "message" => "Receita n??o encontrada",
                'status' => false
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
                'status' => true
            ], 202);
        } else {
            return response()->json([
                "message" => "Receita n??o encontrada",
                'status' => false
            ], 404);
        }
    }
}
