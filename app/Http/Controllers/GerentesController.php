<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\GerentesController;
use App\Models\CredenciaisGerente;
use App\Models\Gerentes;

class GerentesController extends Controller
{

    public function getById(int $id){
        return response()->json([
            "message" => Gerentes::where('codigoUnico', $id)->get()
        ], 200);
    }

    public function deleteById(int $id){
        return response()->json([
            "message" => Gerentes::where('codigoUnico', $id)->delete()
        ], 200);
    }

    public function criarGerente(Request $request){
        //Validando dados da request
        if(!ValidarDadosController::validarNovoGerente($request)){
            return response()->json([
               "message" => "Erro nos parametros passados."
            ],200);
        }
        //Realizando a autenticacao do usuario(gerente)
        $autenticacao = GerentesController::autenticarGerente($request,$request->nivel);
        if($autenticacao->original['message'] != "Gerente foi Autenticado."){
            return response()->json([
                "message" => "Nao foi posssivel autenticar o gerente para realizar essa acao."
            ],200);
        }

        //Criando novo gerente
        $gerente = new Gerentes;
        $gerente->nome = $request->nome;
        $gerente->email = $request->email;
        $gerente->nivel = $request->nivel;
        $gerente->save();

        return response()->json([
            "message" => "Gerente criado com sucesso."
        ],200);
    }

    public function excluirGerente(Request $request){

        //validacao dos parametros passados na request
        if(!ValidarDadosController::validarGerente($request)){
            return response()->json([
               "message" => "Erro nos parametros passados, ou voce nao possui nivel para realizar essa acao."
            ],200);
        }
        //Realizando a autenticacao do usuario(gerente)
        $autenticacao = GerentesController::autenticarGerente($request,$request->nivel);
        $autenticacao = $autenticacao->original['message'];
        if($autenticacao != "Gerente foi Autenticado."){
            return response()->json([
                "message" => "Nao foi posssivel autenticar o gerente para realizar essa acao."
            ],200);
        }

        Gerentes::where('codigoUnico', $request->codigoUnico)->delete();

        return response()->json([
            "message" => "Gerente excluido com sucesso."
        ],200);
    }

    public function editarGerente(Request $request){

        //Validacao dos dados da request
        if(!ValidarDadosController::validarGerente($request)){
            return response()->json([
               "message" => "Erro nos parametros passados."
            ],200);
        }

        //Realiza a autenticacao do ussuario(agerente)
        $autenticacao = GerentesController::autenticarGerente($request,$request->nivel);
        if($autenticacao->original['message'] != "Gerente foi Autenticado."){
            return response()->json([
                "message" => "Nao foi posssivel autenticar o gerente para realizar essa acao."
            ],200);
        }

        //Montando novoGerente
        $novoGerente =[
            'nome' => $request->nome,
            'email' => $request->email,
            'nivel' => $request->nivel
        ];       
        $novoGerente= array_filter($novoGerente);

        Gerentes::where('codigoUnico',$request->codigoUnico)
                ->update($novoGerente);

        return response()->json([
                "message" => "Gerente atualizado."
            ],200); 
    }

    public function autenticarGerente(Request $request, int $nivel=null){

        if (CredenciaisGerenteController::getByToken($request->token)) {
            $credenciaisGerente = CredenciaisGerenteController::getByToken($request->token);
            $gerente = $credenciaisGerente->original["Usuario"][0];
            $credenciais = $credenciaisGerente->original["Credenciais"];

            if(intval($gerente->nivel) < intval($nivel)){
                return response()->json([
                    "message" => "Gerente nao possssui o nivel de acesso necessario para realzar esta acao."
                ], 200);
            }
            
            return response()->json([
                "message" => "Gerente foi Autenticado."
            ], 200);
        }
    
        return response()->json([
            "message" => "Usuario e/ou senha incorretos."
        ], 200);
    }
}
