<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientesGrupos extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigoUnico',
        'codigoCliente',
        'CodigoGrupo'
    ];
}
