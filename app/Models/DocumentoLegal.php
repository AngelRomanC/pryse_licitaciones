<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoLegal extends Documento
{
    use HasFactory;
    protected $connection = 'mysql';  // O el nombre de la conexión que estés usando

    protected $table = 'documentos';

    protected $fillable = [
        'nombre_documento',
        'empresa_id',
        'tipo_de_documento_id',

        'departamento_id',
        'fecha_revalidacion',
        'fecha_vigencia',

        'ruta_documento',
        'ruta_documento_anexo'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDeDocumento::class, 'tipo_de_documento');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id'); //falto
    }

    public function fechas()
    {
        //return $this->hasMany(Fecha::class);
        return $this->hasMany(Fecha::class, 'documento_id');

    }

    //relación con archivosPDF
    public function archivos()
    {
        return $this->hasMany(DocumentoArchivo::class, 'documento_id');
    }
    //Siempre debemos especificar el id, donde se guardar el datop ya que es un Modelo Heredado

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
