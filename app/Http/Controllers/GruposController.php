<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grupos;
use App\Models\ClientesGrupos;
use App\Http\Controllers\GerentesController;
use App\Http\Controllers\ClientesGruposController;

class GruposController extends Controller
{

    public function getById(int $id){
        return response()->json([
            "message" => Grupos::where('codigosUnicos', $id)->get()
        ],200);
    }

    public function deleteById(int $id){
        return response()->json([
            "message" => Grupos::where('codigosUnicos', $id)->delete()
        ],200);
    }

    public function criarGrupo(Request $request){
        
        //Valida os dados passados na request
        if(!ValidarDadosController::validarNovoGrupo($request)){
            return response()->json([
               "message" => "Erro nos parametros passados."
            ],200);
        }

        //Realiza a autenticacao do usuario(gerente)
        $autenticacao = GerentesController::autenticarGerente($request,2);
        if($autenticacao->original['message'] != "Gerente foi Autenticado."){
            return response()->json([
                "message" => "Nao foi posssivel autenticar o gerente, ou nao possui nivel suficiente para realizar esta acao."
            ],200);
        }

        //Criando novo grupo
        $grupo = new Grupos;
        $grupo->nome = $request->nome;
        $grupo->save();

        return response()->json([
            "message" => "Grupo criado com sucesso."
        ],200);
    }


    public function excluirGrupo(Request $request){
        
        //Realiza a autenticacao do usuario(gerente)
        $autenticacao = GerentesController::autenticarGerente($request,2);
        if($autenticacao->original['message'] != "Gerente foi Autenticado."){
            return response()->json([
                "message" => "Nao foi posssivel autenticar o gerente, ou nao possui nivel suficiente para realizar esta acao."
            ],200);
        }

        Grupos::where('codigoUnico',$request->codigoGrupo)->delete();

        return response()->json([
            "message" => "Grupo excluido com sucessso."
        ],200);
    }


    public function editarGrupo(Request $request){

        //Valida os dados na request
        if(!ValidarDadosController::validarGrupo($request)){
            return response()->json([
               "message" => "Erro nos parametros passados."
            ],200);
        }

        //Realiza a autenticacao do usuario(gerente)
        $autenticacao = GerentesController::autenticarGerente($request,2);
        if($autenticacao->original['message'] != "Gerente foi Autenticado."){
            return response()->json([
                "message" => "Nao foi posssivel autenticar o gerente, ou nao possui nivel suficiente para realizar esta acao."
            ],200);
        }

        //Cria objeto para atualizar o grupo
        $novoGrupo =[
            'nome' => $request->nome,
            ];       
        $novoGrupo= array_filter($novoGrupo);

        Grupos::where('codigoUnico',$request->codigoUnico)
                ->update($novoGrupo);

        return response()->json([
                "message" => "Grupo atualizado."
            ],200); 
    }


    public function visualizarGrupos(Request $request){

        //Realiza a autenticacao do usuario(gerente)
        $autenticacao = GerentesController::autenticarGerente($request);
        if($autenticacao->original['message'] != "Gerente foi Autenticado."){
            return response()->json([
                "message" => "Nao foi posssivel autenticar o gerente."
            ],200);
        }

        $grupos = Grupos::all();

        return response()->json([
            "message" => $grupos
        ],200);
    }


    public function getClientesDoGrupo(Request $request){
        
        //Realiza a autenticacao do usuario(gerente)
        $autenticacao = GerentesController::autenticarGerente($request);
        if($autenticacao->original['message'] != "Gerente foi Autenticado."){
            return response()->json([
                "message" => "Nao foi posssivel autenticar o gerente."
            ],200);
        }

        $listaClientes = ClientesGrupos::where('codigoGrupo', $request->codigoGrupo)->get();
        
        if($listaClientes == null){
            return response()->json([
                "message" => "Nenhum cliente encontrado nesse grupo.",
            ], 200);
        }

        return response()->json([
            "message" => "Clientes encontrados.",
            "clientes" => $listaClientes
        ], 200);
    }
}
