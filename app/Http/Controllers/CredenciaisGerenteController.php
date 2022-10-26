<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CredenciaisGerente;
use App\Models\Gerentes;

class CredenciaisGerenteController extends Controller
{

    //Pega os dados do gerente pelo seu nome de usuario
    public function getByUsuario(string $usuario){
        if(CredenciaisGerente::where('Usuario', $usuario)->exists()){
            $credenciasGerente = CredenciaisGerente::where('Usuario', $usuario)->get();
            $credenciasGerente = $credenciasGerente[0];
            $credencias = Gerentes::where('CodigoUnico', $credenciasGerente->CodigoGerente)
                                  ->get();
            
            return response()->json([
                "Usuario" => $credencias,
                "Credenciais" => $credenciasGerente
            ], 200);
        }

        return response()->json([
            "message" => "Usuario nao encontrado"
        ], 200);
    }

    //Pega os dados do gerente pelo token de autenticacao.
    public function getByToken(string $token){

        if(CredenciaisGerente::where('accessToken', $token)->exists()){
            $credenciasGerente = CredenciaisGerente::where('accessToken', $token)->get();
            $credenciasGerente = $credenciasGerente[0];
            $credencias = Gerentes::where('CodigoUnico', $credenciasGerente->CodigoGerente)
                                  ->get();
            
            return response()->json([
                "Usuario" => $credencias,
                "Credenciais" => $credenciasGerente
            ], 200);
        }

        return false;
    }
}
