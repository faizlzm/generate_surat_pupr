<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuditReferenceGroup extends Model
{
    /** @use HasFactory<\Database\Factories\AuditReferenceGroupFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'references_json',
    ];

    protected function casts(): array
    {
        return [
            'references_json' => 'array',
        ];
    }

    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class);
    }

    /**
     * @return array<int, string>
     */
    public function references(): array
    {
        $refs = $this->references_json;
        if (! is_array($refs)) {
            return [];
        }

        return array_values(array_map(static fn ($v) => (string) $v, $refs));
    }
}
