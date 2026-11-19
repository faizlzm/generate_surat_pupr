<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'nip',
        'rank',
        'grade',
        'position',
    ];

    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class);
    }

    public function getPangkatGolonganAttribute(): string
    {
        $parts = array_filter([
            $this->grade ? trim((string) $this->grade) : null,
            $this->rank ? '('.trim((string) $this->rank).')' : null,
        ]);

        return implode(' ', $parts);
    }
}
