<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Empleado extends Model
{
    protected $fillable = [
        'dni','apellidos','nombres','fecha_nacimiento','direccion','celular','email',
        'sexo','estado','fecha_ingreso','tipo_trabajador','sueldo','area_id','cargo_id',
    ];

    public function area(): BelongsTo { return $this->belongsTo(Area::class); }
    public function cargo(): BelongsTo { return $this->belongsTo(Cargo::class); }

    public function marcaciones(): HasMany { return $this->hasMany(Marcacion::class); }
    public function asistencias(): HasMany { return $this->hasMany(AsistenciaDia::class); }
    public function ausencias(): HasMany { return $this->hasMany(Ausencia::class); }
}
