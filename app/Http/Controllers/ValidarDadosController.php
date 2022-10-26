<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\GruposController;
use App\Models\Grupos;
use App\Models\Gerentes;
use App\Models\Clientes;
use App\Models\CredenciaisGerente;


class ValidarDadosController extends Controller
{
    //validacoes de dados mais basicos cpf,cnpj,email...
    public function validarCnpj(string $cnpj){
    
        /**
        * Valida CNPJ
        *
        * @author Luiz Otávio Miranda <contato@todoespacoonline.com/w>
        * @param string $cnpj 
        * @return bool true para CNPJ correto
        *
        */
        $cnpj = preg_replace( '/[^0-9]/', '', $cnpj );
    
        // Garante que o CNPJ é uma string
        $cnpj = (string)$cnpj;
        
        // O valor original
        $cnpj_original = $cnpj;
        
        // Captura os primeiros 12 números do CNPJ
        $primeiros_numeros_cnpj = substr( $cnpj, 0, 12 );
        
        /**
         * Multiplicação do CNPJ
         *
         * @param string $cnpj Os digitos do CNPJ
         * @param int $posicoes A posição que vai iniciar a regressão
         * @return int O
         *
         */
        if ( ! function_exists('multiplica_cnpj') ) {
            function multiplica_cnpj( $cnpj, $posicao = 5 ) {
                // Variável para o cálculo
                $calculo = 0;
                
                // Laço para percorrer os item do cnpj
                for ( $i = 0; $i < strlen( $cnpj ); $i++ ) {
                    // Cálculo mais posição do CNPJ * a posição
                    $calculo = $calculo + ( $cnpj[$i] * $posicao );
                    
                    // Decrementa a posição a cada volta do laço
                    $posicao--;
                    
                    // Se a posição for menor que 2, ela se torna 9
                    if ( $posicao < 2 ) {
                        $posicao = 9;
                    }
                }
                // Retorna o cálculo
                return $calculo;
            }
        }

        // Faz o primeiro cálculo
        $primeiro_calculo = multiplica_cnpj( $primeiros_numeros_cnpj );

        // Se o resto da divisão entre o primeiro cálculo e 11 for menor que 2, o primeiro
        // Dígito é zero (0), caso contrário é 11 - o resto da divisão entre o cálculo e 11
        $primeiro_digito = ( $primeiro_calculo % 11 ) < 2 ? 0 :  11 - ( $primeiro_calculo % 11 );

        // Concatena o primeiro dígito nos 12 primeiros números do CNPJ
        // Agora temos 13 números aqui
        $primeiros_numeros_cnpj .= $primeiro_digito;
 
        // O segundo cálculo é a mesma coisa do primeiro, porém, começa na posição 6
        $segundo_calculo = multiplica_cnpj( $primeiros_numeros_cnpj, 6 );
        $segundo_digito = ( $segundo_calculo % 11 ) < 2 ? 0 :  11 - ( $segundo_calculo % 11 );

        // Concatena o segundo dígito ao CNPJ
        $cnpj = $primeiros_numeros_cnpj . $segundo_digito;

        // Verifica se o CNPJ gerado é idêntico ao enviado
        if ( $cnpj === $cnpj_original ) {
            return true;
        }

    }

    public function validarEmail(string $email){

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
           return false;
        }
        return true;
    }
    //==============

    //validarModel => usado para qualquer tipo de alteracao do dado (edit)
    //validarNovaModel => usado para validar novas entradas de dados(create)

    public function validarCliente(object $request){

        //Validar codigo do cliente
        if(is_null($request->codigoUnico)){
            return false;
        }
        if(!Clientes::where('codigoUnico',$request->codigoUnico)->exists()){
            return false;
        }

        //validar cnpj inserido
        if(!is_null($request->cnpj)){
            if(is_null(ValidarDadosController::validarCnpj($request->cnpj))){
                return false;
            }
        }

        //validar se grupo existe
        if(!is_null($request->codigoGrupo)){
            if(json_encode(Grupos::where('codigoUnico' ,$request->codigoGrupo)->get()) == "[]"){
                return false;
            }               
        }

        return true;
    }

    public function validarNovoCliente(object $request){

        //validar cnpj inserido
        if(is_null($request->cnpj)){
            return false;
        }

        //validar dataFundacao
        if(is_null($request->data)){
          return false;
        }

        return true;
    }
    
    public function validarGerente(object $request){

          //Validar codigo do cliente
        if(is_null($request->codigoUnico)){
            return false;
        }
        if(!Gerentes::where('codigoUnico',$request->codigoUnico)->exists()){
            return false;
        }
        
        if(!is_null($request->email)){
            if(ValidarDadosController::validarEmail($request->email)){
                //return false;
            }
        } 
        
        //Valida se gerente posssui nivel para editar um gerente.
        $gerenteEditando = CredenciaisGerente::where('accessToken',$request->token)->get();
        $gerenteEditando = Gerentes::where('codigoUnico',$gerenteEditando[0]->CodigoGerente)->get();
        $gerenteParaEditar = Gerentes::where('codigoUnico', $request->codigoUnico)->get();
        if($gerenteEditando[0]->nivel < $gerenteParaEditar[0]->nivel){
            return false;
        } 

        return true;
    }

    public function validarNovoGerente(object $request){

        if(is_null($request->nome)){
            return false;
        }

        if(is_null($request->email)){
            return false;
        } 
        if(ValidarDadosController::validarEmail($request->email)){
            //return false;
        }

        if(is_null($request->nivel) && $request->nivel >= 0){
            return false;
        }

        return true;
    }

    public function validarGrupo(object $request){

        if(is_null($request->codigoUnico)){
            return false;
        }
        if(Grupos::where('codigoUnico',$request->codigoUnico)->get()->first() ==null){
            return false;
        }
        
        if(is_null($request->nome)){
            return false;
        }

        return true;
    }

    public function validarNovoGrupo(object $request){
        if(is_null($request->nome)){
            return false;
        }

        return true;
    }
}
