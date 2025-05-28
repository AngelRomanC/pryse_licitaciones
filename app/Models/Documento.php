<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre_documento',
        'empresa_id',
        'tipo_de_documento_id',
        'estado_id',
        'departamento_id',
        'fecha_revalidacion',
        'fecha_vigencia',
        'modalidad_id',
        'ruta_documento',
        'ruta_documento_anexo'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function tipoDeDocumento()
    {
        return $this->belongsTo(TipoDeDocumento::class, );
    }
    public function estado()
    {
        return $this->belongsTo(Estado::class); //falto
    }
    public function departamento()
    {
        return $this->belongsTo(Departamento::class); //falto
    }

    public function fechas()
    {
        return $this->hasMany(Fecha::class);
    }

    public function modalidades()
    {
        return $this->belongsToMany(Modalidad::class, 'documentos_modalidades') ->withTimestamps();

    }

    //relación con archivosPDF
    public function archivos() {
        return $this->hasMany(DocumentoArchivo::class);
    }

}
