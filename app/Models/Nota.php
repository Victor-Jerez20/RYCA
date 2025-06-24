<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    use HasFactory;

    protected $table = 'notas';
    protected $primaryKey = 'id_nota';

    protected $fillable = [
        'carne',
        'id_curso',
        'consolidado',
        'fecha_aprobacion',
    ];

    public $timestamps = false;
}
