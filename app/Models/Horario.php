<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $fillable = [
        'nombre','ingreso','salida','tolerancia_min','refrigerio_inicio','refrigerio_fin'
    ];
}
