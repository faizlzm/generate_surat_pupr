<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedDocument extends Model
{
    /** @use HasFactory<\Database\Factories\GeneratedDocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'letter_id',
        'file_path',
        'page_count',
        'template_version',
        'generated_at',
        'checksum',
    ];

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
            'page_count' => 'integer',
        ];
    }

    public function letter(): BelongsTo
    {
        return $this->belongsTo(Letter::class);
    }
}
