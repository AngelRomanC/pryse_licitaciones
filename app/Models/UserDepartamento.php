<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDepartamento extends Model
{
    use HasFactory;

    protected $table = 'user_departamento';

    protected $fillable = ['user_id', 'departamento_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }
    
    //Relación para obtener información del departamento en Dashboard Vencidos etc..
    public function infoDepartamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id', 'id');
    }
}
