<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    public    $timestamps = false;
    protected $fillable   = ['name', 'type', 'position'];

    public function scopeGastos(Builder $q): Builder  { return $q->where('type', 'gasto'); }
    public function scopeIngresos(Builder $q): Builder { return $q->where('type', 'ingreso'); }
    public function scopeOrdenadas(Builder $q): Builder { return $q->orderBy('name'); }
}
