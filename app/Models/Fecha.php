<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fecha extends Model
{
    use HasFactory;
    protected $fillable = [
        'documento_id', // Clave foránea al documento
        'fecha_revalidacion',
        'fecha_vigencia',
    ];

    public function documento()
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }

    public function documentoLegal()
    {
        return $this->belongsTo(DocumentoLegal::class, 'documento_id');
    }
}
