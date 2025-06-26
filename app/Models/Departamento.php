<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Departamento extends Model
{
    use HasFactory;
    use Notifiable;
    protected $fillable = ['nombre_departamento', 'email'];

    public function routeNotificationForMail()
    {
        return $this->email;
    }
}
