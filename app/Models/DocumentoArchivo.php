<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoArchivo extends Model
{
    use HasFactory;
    protected $fillable = [
        'documento_id',
        'ruta_archivo',
        'tipo',        
        'nombre_original',       
    ];

    public function documento() {
        return $this->belongsTo(Documento::class);
    }
   
}
