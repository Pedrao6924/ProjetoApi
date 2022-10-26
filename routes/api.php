;<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Gerentes;
use App\Models\Clientes;
use App\Models\Grupos;


//Gerentes
Route::get('/gerentes', function(){return Gerentes::all();});
Route::get('/autenticarGerente','App\Http\Controllers\GerentesController@autenticarGerente');
Route::get('/criarGerente','App\Http\Controllers\GerentesController@criarGerente');
Route::get('/excluirGerente','App\Http\Controllers\GerentesController@excluirGerente');
Route::get('/editarGerente','App\Http\Controllers\GerentesController@editarGerente');

//Clientes
Route::get('/clientes', function(){return Clientes::all();});
Route::post('/criarCliente', 'App\Http\Controllers\ClientesController@criarCliente');
Route::delete('/excluirCliente', 'App\Http\Controllers\ClientesController@excluirCliente');
Route::get('/transferirCliente', 'App\Http\Controllers\ClientesController@transferirCliente');
Route::get('/editarCliente', 'App\Http\Controllers\ClientesController@editarCliente');

//Grupos
Route::get('/grupos', function(){return Grupos::all();});
Route::get('/visualizarGrupos', 'App\Http\Controllers\GruposController@visualizarGrupos');
Route::get('/clientesGrupo', 'App\Http\Controllers\GruposController@getClientesDoGrupo');
Route::post('/criarGrupo', 'App\Http\Controllers\GruposController@criarGrupo');
Route::delete('/excluirGrupo', 'App\Http\Controllers\GruposController@excluirGrupo');
Route::get('/editarGrupo', 'App\Http\Controllers\GruposController@editarGrupo');
