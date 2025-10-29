<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Marcacion extends Model
{
    use HasFactory;

    /** Nombre real de la tabla (si usas 'marcacions' déjalo así) */
    protected $table = 'marcacions';

    /** La tabla SÍ tiene created_at / updated_at (según tu schema) */
    public $timestamps = true;

    protected $fillable = [
        'empleado_id',
        'timestamp',
        'tipo',            // opcional (puede venir null y se infiere en el builder)
        'origen',          // ej. 'zkteco_n8n'
        'hash_seguridad',  // único (userSn)
    ];

    /** Casts: ¡muy importante para evitar errores ->between() on string! */
    protected $casts = [
        'timestamp'  => 'datetime',          // Carbon
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** Relación */
    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    /* ─────── Scopes útiles ─────── */

    /** Filtrar por empleado */
    public function scopeDeEmpleado($q, int $empleadoId)
    {
        return $q->where('empleado_id', $empleadoId);
    }

    /** Rango de fecha/hora (acepta Carbon|string) */
    public function scopeEntre($q, $desde, $hasta)
    {
        $d = $desde instanceof Carbon ? $desde : Carbon::parse($desde);
        $h = $hasta instanceof Carbon ? $hasta : Carbon::parse($hasta);
        return $q->whereBetween('timestamp', [$d, $h]);
    }

    /** Solo las que vienen del webhook ZKTeco (ajusta si usas otro origen) */
    public function scopeZkteco($q)
    {
        return $q->where('origen', 'zkteco_n8n');
    }

    /** Orden recientes por la propia marca (por si no quieres usar updated_at) */
    public function scopeRecientes($q)
    {
        return $q->orderByDesc('timestamp');
    }
}
