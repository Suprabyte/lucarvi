<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ausencia extends Model
{
    protected $fillable = [
        'empleado_id',
        'fecha',
        'tipo',
        'motivo',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
