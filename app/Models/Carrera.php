<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $table = 'carreras';
    protected $primaryKey = 'id_carrera';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_carrera',
        'nombre',
    ];

    // Relación con cursos
    public function cursos()
    {
        return $this->hasMany(Curso::class, 'id_carrera', 'id_carrera');
    }
}
