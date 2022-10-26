<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gerentes extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigoUnico',
        'nome',
        'email',
        'nivel'
    ];
}
