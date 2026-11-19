<?php

declare(strict_types=1);

namespace App\Services\WordGenerator;

use InvalidArgumentException;

final class BatchValidator
{
    /**
     * @return list<string>
     */
    public static function validate(array $payload): array
    {
        $errors = [];
        $common = $payload['informasi_bersama'] ?? null;
        $groups = $payload['kelompok_audit'] ?? null;
        $recipients = $payload['penerima'] ?? null;

        if (! is_array($common)) {
            return ['Bagian informasi_bersama tidak ditemukan.'];
        }
        foreach (['tanggal_surat', 'jabatan_pengirim', 'jabatan_penandatangan', 'nama_penandatangan'] as $field) {
            if (! self::filled($common[$field] ?? null)) {
                $errors[] = "Informasi bersama: {$field} wajib diisi.";
            }
        }
        if (! is_array($common['tembusan'] ?? null) || count($common['tembusan']) === 0) {
            $errors[] = 'Informasi bersama: minimal satu tembusan wajib diisi.';
        } else {
            foreach ($common['tembusan'] as $j => $entry) {
                if (! self::filled($entry)) {
                    $errors[] = 'Tembusan nomor '.($j + 1).' kosong.';
                }
            }
        }

        if (! is_array($groups)) {
            $errors[] = 'Daftar kelompok_audit harus berupa array.';
            $groups = [];
        }
        $byId = [];
        foreach ($groups as $j => $g) {
            if (! is_array($g) || ! self::filled($g['id'] ?? null)) {
                $errors[] = 'Kelompok audit nomor '.($j + 1).' tidak memiliki ID.';
                continue;
            }
            $id = (string) $g['id'];
            if (isset($byId[$id])) {
                $errors[] = "ID kelompok audit {$id} terduplikasi.";
            }
            $byId[$id] = $g;
            if (! self::validReferences($g['referensi'] ?? null)) {
                $errors[] = "Kelompok audit {$id}: referensi wajib berisi teks tidak kosong.";
            }
        }

        if (! is_array($recipients) || count($recipients) < 1) {
            $errors[] = 'Minimal satu penerima harus tersedia.';

            return $errors;
        }
        $numbers = [];
        foreach ($recipients as $i => $r) {
            $row = $i + 1;
            if (! is_array($r)) {
                $errors[] = "Penerima {$row}: data harus berupa objek.";
                continue;
            }
            foreach (['nomor_memorandum', 'nama', 'nip', 'jabatan', 'alasan_teguran', 'sapaan'] as $key) {
                if (! self::filled($r[$key] ?? null)) {
                    $errors[] = "Penerima {$row}: {$key} wajib diisi.";
                }
            }
            if (! self::filled($r['pangkat_golongan'] ?? null) &&
                (! self::filled($r['golongan'] ?? null) || ! self::filled($r['pangkat'] ?? null))) {
                $errors[] = "Penerima {$row}: pangkat_golongan atau kombinasi pangkat dan golongan wajib diisi.";
            }
            if (! is_string($r['nip'] ?? null) || ! preg_match('/^\d{18}$/D', $r['nip'])) {
                $errors[] = "Penerima {$row}: NIP harus bertipe teks dan tepat 18 digit.";
            }
            if (! in_array($r['sapaan'] ?? null, ['Saudara', 'Saudari'], true)) {
                $errors[] = "Penerima {$row}: sapaan hanya boleh Saudara atau Saudari.";
            }
            if (self::filled($r['nomor_memorandum'] ?? null)) {
                $num = trim((string) $r['nomor_memorandum']);
                if (isset($numbers[$num])) {
                    $errors[] = "Nomor memorandum {$num} digunakan penerima {$numbers[$num]} dan {$row}.";
                }
                $numbers[$num] = $row;
            }
            $custom = $r['referensi_khusus'] ?? null;
            if ($custom !== null && $custom !== []) {
                if (! self::validReferences($custom)) {
                    $errors[] = "Penerima {$row}: referensi khusus tidak valid.";
                }
            } else {
                $groupId = (string) ($r['kelompok_audit_id'] ?? '');
                if ($groupId === '' || ! isset($byId[$groupId])) {
                    $errors[] = "Penerima {$row}: kelompok audit tidak ditemukan.";
                }
            }
        }

        return $errors;
    }

    /**
     * @param  array<string, array<string, mixed>>  $groupById
     * @return list<string>
     */
    public static function referencesFor(array $recipient, array $groupById): array
    {
        $custom = $recipient['referensi_khusus'] ?? null;
        if (is_array($custom) && count($custom) > 0) {
            return array_values($custom);
        }
        $groupId = (string) ($recipient['kelompok_audit_id'] ?? '');
        if (! isset($groupById[$groupId])) {
            throw new InvalidArgumentException("Kelompok audit {$groupId} tidak ditemukan.");
        }

        $refs = $groupById[$groupId]['referensi'] ?? [];

        return array_values(is_array($refs) ? $refs : []);
    }

    private static function filled(mixed $value): bool
    {
        return is_string($value) && trim($value) !== '';
    }

    private static function validReferences(mixed $references): bool
    {
        if (! is_array($references) || count($references) === 0) {
            return false;
        }
        foreach ($references as $ref) {
            if (! self::filled($ref)) {
                return false;
            }
            try {
                ReferenceFormatter::clean($ref);
            } catch (InvalidArgumentException) {
                return false;
            }
        }

        return true;
    }
}
