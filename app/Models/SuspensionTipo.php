<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuspensionTipo extends Model
{
    protected $fillable = ['tipo', 'codigo', 'descripcion', 'activo'];
}
