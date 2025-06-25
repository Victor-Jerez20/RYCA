<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    protected $table = 'sedes';
    protected $primaryKey = 'id_sede';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_sede',
        'nombre',
    ];

    // Relación con estudiantes
    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class, 'id_sede', 'id_sede');
    }

    // Relación con notas (si las notas tienen un campo id_sede directamente)
    public function notas()
    {
        return $this->hasMany(Nota::class, 'id_sede', 'id_sede');
    }
}
