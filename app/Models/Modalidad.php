<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modalidad extends Model
{
    use HasFactory;
    protected $fillable = ['nombre_modalidad'];
    public function documentos()
{
    return $this->belongsToMany(Documento::class, 'documentos_modalidades');
}

}
