<?php

declare(strict_types=1);

namespace App\Services\WordGenerator;

use InvalidArgumentException;

final class ReferenceFormatter
{
    /**
     * Input referensi disimpan tanpa tanda baca enumerasi akhir.
     */
    public static function clean(string $value): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $value) ?? $value);
        // Hilangkan akhiran penomoran lama, namun jangan mengubah tanda baca di tengah teks.
        $text = preg_replace('/\s*;\s*dan\s*$/iu', '', $text) ?? $text;
        $text = preg_replace('/[;.]+\s*$/u', '', $text) ?? $text;
        $text = trim($text);

        if ($text === '') {
            throw new InvalidArgumentException('Referensi audit tidak boleh kosong.');
        }

        return $text;
    }

    public static function ending(int $position, int $total): string
    {
        if ($total < 1 || $position < 1 || $position > $total) {
            throw new InvalidArgumentException('Posisi referensi tidak valid.');
        }

        return $position === $total ? '.' : ($position === $total - 1 ? '; dan' : ';');
    }

    /**
     * @param  list<string>  $references
     * @return list<array{number:string,text:string,ending:string}>
     */
    public static function format(array $references): array
    {
        $total = count($references);
        if ($total === 0) {
            throw new InvalidArgumentException('Minimal satu referensi audit diperlukan.');
        }

        $items = [];
        foreach (array_values($references) as $index => $text) {
            if (! is_string($text)) {
                throw new InvalidArgumentException('Isi referensi harus berupa teks.');
            }
            $position = $index + 1;
            $items[] = [
                'number' => $position.'.',
                'text' => self::clean($text),
                'ending' => self::ending($position, $total),
            ];
        }

        return $items;
    }
}
