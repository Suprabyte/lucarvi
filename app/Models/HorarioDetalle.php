<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioDetalle extends Model
{
    protected $table = 'horarios_detalle';

    protected $fillable = [
        'area_id',
        'cargo_id',
        'hora_ingreso',
        'hora_salida',
        'refrigerio_ingreso',
        'refrigerio_salida',
        'observacion',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }
}
