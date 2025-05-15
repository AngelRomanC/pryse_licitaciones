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
        'empresa_id',
        'estado_id',



    ];
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function documentosTecnicos()
    {
        return $this->hasMany(Documento::class);
    }

    public function documentosLegales()
    {
        return $this->hasMany(DocumentoLegal::class);
    }
}
