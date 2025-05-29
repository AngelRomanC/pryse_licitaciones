<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Licitacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'fecha',


    ];
    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'empresa_licitacion')->withTimestamps();
    }

    public function estados()
    {
        return $this->belongsToMany(Estado::class, 'estado_licitacion')->withTimestamps();
    }
    public function archivos()
    {
        return $this->belongsToMany(DocumentoArchivo::class, 'archivo_licitacion')->withPivot('tipo')->withTimestamps();
    }

    public function modalidades()
    {
        return $this->belongsToMany(Modalidad::class, 'modalidad_licitacion')->withTimestamps();
    }

}
