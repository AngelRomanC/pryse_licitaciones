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
        'ruta_documento_anexo',
        'user_id',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function tipoDeDocumento()
    {
        return $this->belongsTo(TipoDeDocumento::class, ); //Recuerda que el nombre de función es la que usaras en el Controlador Docuement
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
        return $this->belongsToMany(Modalidad::class, 'documentos_modalidades')->withTimestamps();

    }

    //relación con archivosPDF
    public function archivos()
    {
        return $this->hasMany(DocumentoArchivo::class);
    }
    // ✅ Relación con el usuario creador
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    protected static function booted()
    {
        static::creating(function ($documento) {
            // Solo asigna auth()->id() si user_id no está definido
            if (empty($documento->user_id)) {
                $documento->user_id = auth()->id();
            }
        });
    }

}
