<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Letter extends Model
{
    /** @use HasFactory<\Database\Factories\LetterFactory> */
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_FINAL = 'final';

    protected $fillable = [
        'letter_number',
        'letter_date',
        'status',
        'employee_id',
        'audit_reference_group_id',
        'payload_json',
    ];

    protected function casts(): array
    {
        return [
            'letter_date' => 'date',
            'payload_json' => 'array',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function auditReferenceGroup(): BelongsTo
    {
        return $this->belongsTo(AuditReferenceGroup::class);
    }

    public function generatedDocuments(): HasMany
    {
        return $this->hasMany(GeneratedDocument::class);
    }

    public function latestDocument(): ?GeneratedDocument
    {
        return $this->generatedDocuments()->latest('generated_at')->latest('id')->first();
    }

    public function isFinal(): bool
    {
        return $this->status === self::STATUS_FINAL;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        $payload = $this->payload_json;

        return is_array($payload) ? $payload : [];
    }
}
