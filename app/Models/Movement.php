<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Movement extends Model
{
    protected $fillable = ['amount', 'type', 'category', 'description', 'date', 'person'];

    protected $casts = [
        'date'   => 'date',
        'amount' => 'decimal:2',
    ];

    public function getPersonNameAttribute(): string
    {
        return config('personas.' . $this->person, ucfirst(str_replace('_', ' ', $this->person)));
    }

    public function scopeForPeriod(Builder $query, string $periodType, int $year, ?int $month = null): Builder
    {
        $query->whereYear('date', $year);

        if ($periodType === 'month' && $month) {
            $query->whereMonth('date', $month);
        }

        return $query;
    }

    public function scopeForPerson(Builder $query, ?string $person): Builder
    {
        if ($person) {
            $query->where('person', $person);
        }

        return $query;
    }

    public function scopeForCategory(Builder $query, ?string $category): Builder
    {
        if ($category) {
            $query->where('category', $category);
        }

        return $query;
    }
}
