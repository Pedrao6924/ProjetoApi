<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesGruposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes_grupos', function (Blueprint $table) {
            $table->id('codigoUnico');
            $table->integer('codigoCliente');
            $table->foreign('codigoCliente')->references('codigoUnico')->on('clientes');
            $table->integer('codigoGrupo');
            $table->foreign('codigoGrupo')->references('codigoUnico')->on('grupos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientes_grupos');
    }
}
