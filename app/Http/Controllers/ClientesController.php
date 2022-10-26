<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clientes;
use App\Http\Controllers\GerentesController;
use App\Http\Controllers\ValidarDadosController;


class ClientesController extends Controller
{

    public function getById(int $id){
        return response()->json([
            "message" => Clientes::where('codigoUnico',$id)->get()
        ],200);
    }

    public function deleteById(int $id){
        return response()->json([
            "message" => Clientes::where('codigoUnico',$id)->delete()
        ],200);
    }

    public function criarCliente(Request $request){

        //Validando os dados passados no request
        if(!ValidarDadosController::validarNovoCliente($request)){
            return response()->json([
               "message" => "Erro nos parametros passados."
            ],200);
        }

        //Realiza a autenticacao do usuario(gerente)
        $autenticacao = GerentesController::autenticarGerente($request);
        if($autenticacao->original['message'] != "Gerente foi Autenticado."){
            return response()->json([
                "message" => "Nao foi posssivel autenticar o gerente."
            ],200);
        }

        //Criando novo cliente
        $clientes = new Clientes;
        $clientes->cnpj = $request->cnpj;
        $clientes->nome = $request->nome;
        $clientes->dataFundacao = $request->data;
        $clientes->save();

        return response()->json([
            "message" => "Cliente criado com sucesso."
        ],200);
    }


    public function excluirCliente(Request $request){

        //Realiza a autenticacao do usuario(gerente)
        $autenticacao = GerentesController::autenticarGerente($request);
        if($autenticacao->original['message'] != "Gerente foi Autenticado."){
            return response()->json([
                "message" => "Nao foi posssivel autenticar o gerente."
            ],200);
        }

        //Excluindo cliente
        if(Clientes::where('codigoUnico', $request->codigoUnico)->exists()) {
            $cliente = Clientes::where('codigoUnico', $request->codigoUnico);
            $cliente->delete();

            return response()->json([
              "message" => "Cliente excluido."
            ], 202);
          }

        return response()->json([
          "message" => "Cliente nao enconcontrado."
        ], 404);
    }

    public function editarCliente(Request $request){

        //Validando os dados passados na request
        if(!ValidarDadosController::validarCliente($request)){
            return response()->json([
               "message" => "Erro nos parametros passados."
            ],200);
        }

        //Realizando a autenticacao do usuario (gerente)
        $autenticacao = GerentesController::autenticarGerente($request,2);
        if($autenticacao->original['message'] != "Gerente foi Autenticado."){
            return response()->json([
                "message" => "Nao foi posssivel autenticar o gerente, ou nao possui nivel suficiente para realizar a acao."
            ],200);
        }

        //Craindo o obejto novoCliente
        $novoCliente =[
            'nome' => $request->nome,
            'cnpj' => $request->cnpj,
            'codigoGrupo' => $request->codigoGrupo,
            'dataFundacao' => $request->dataFundacao
        ];   
        //filetendo valores nulos    
        $novoCliente= array_filter($novoCliente);

        Clientes::where('codigoUnico',$request->codigoUnico)
                ->update($novoCliente);

        return response()->json([
                "message" => "Cliente atualizado."
            ],200);
    }

    //Realiza a transferencia do usuario de um grupo para outro.
    public function transferirCliente(Request $request){

        //Realiza a autenticacao do usuario(gerente)
        $autenticacao = GerentesController::autenticarGerente($request,1);
        if($autenticacao->original['message'] != "Gerente foi Autenticado."){
            return response()->json([
                "message" => "Nao foi posssivel autenticar o gerente, ou nao possui nivel suficiente para realizar a acao."
            ],200);
        }

        if (Clientes::where('codigoUnico', $request->codigoCliente)->exists()) {
            Clientes::where('codigoUnico', $request->codigoCliente)
                    ->update(['codigoGrupo'=>$request->codigoGrupo]);      

            return response()->json([
                "message" => "Grupo do cliente foi atualizado."
            ], 200);
        }

        return response()->json([
            "message" => "Cliente nao encontrado."
        ], 200);
    }
}
