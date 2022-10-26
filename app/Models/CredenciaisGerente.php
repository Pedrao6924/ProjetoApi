<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CredenciaisGerente extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigoUnicoGerente',
        'usuario',
        'senhna'
    ];
}
