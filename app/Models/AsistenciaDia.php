<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsistenciaDia extends Model
{
    protected $fillable = [
        'empleado_id','fecha','tipo_suspension',
        'hora_ingreso','hora_salida_refrigerio','hora_retorno_refrigerio','hora_salida',
        'min_trabajados','min_tardanza','min_extra_25','min_extra_35','min_extra_100',
        'bloqueado','bloqueo_hash',
    ];
    public function empleado(): BelongsTo { return $this->belongsTo(Empleado::class); }
}
