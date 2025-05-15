<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'descripcion', 'direccion', 'telefono', 'email'];

    public function estados()
    {
        return $this->belongsToMany(Estado::class);
    }

    public function documentosLegales()
    {
        return $this->hasMany(DocumentoLegal::class);
    }

    public function documentosTecnicos()
    {
        return $this->hasMany(Documento::class);
    }

}
