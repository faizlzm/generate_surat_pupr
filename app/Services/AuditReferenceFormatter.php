<?php

namespace App\Services;

class AuditReferenceFormatter
{
    /**
     * Normalize, filter, and format audit references with numbering and punctuation.
     *
     * @param  array<int, string|null>  $references
     * @return array<int, array{index:int,text:string,formatted:string,suffix:string}>
     */
    public function format(array $references): array
    {
        $texts = $this->normalizedTexts($references);
        $count = count($texts);
        $result = [];
        foreach ($texts as $i => $text) {
            $position = $i + 1;
            $suffix = $this->suffix($position, $count);
            $result[] = [
                'index' => $position,
                'text' => $text,
                'formatted' => $position.'. '.$text.$suffix,
                'suffix' => $suffix,
            ];
        }

        return $result;
    }

    /**
     * @param  array<int, string|null>  $references
     * @return array<int, string>
     */
    public function formattedLines(array $references): array
    {
        return array_map(static fn (array $item): string => $item['formatted'], $this->format($references));
    }

    /**
     * @param  array<int, string|null>  $references
     * @return array<int, string>
     */
    public function normalizedTexts(array $references): array
    {
        $clean = [];
        foreach ($references as $reference) {
            $normalized = $this->normalize($reference);
            if ($normalized !== '') {
                $clean[] = $normalized;
            }
        }

        return array_values($clean);
    }

    public function normalize(mixed $value): string
    {
        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return '';
        }

        // Repeatedly strip formatter artifacts copied from prior output:
        // trailing "; dan" (optionally followed by terminators/space) and trailing terminators.
        $previous = null;
        while ($previous !== $value) {
            $previous = $value;
            $value = rtrim($value);
            $value = preg_replace('/(?:;\s*dan)\s*$/iu', '', $value) ?? $value;
            $value = rtrim($value);
            $value = preg_replace('/[;\.]+$/u', '', $value) ?? $value;
            $value = rtrim($value);
        }

        return trim($value);
    }

    public function suffix(int $position, int $count): string
    {
        if ($count <= 1 || $position === $count) {
            return '.';
        }
        if ($position === $count - 1) {
            return '; dan';
        }

        return ';';
    }
}
