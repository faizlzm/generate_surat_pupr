<?php

declare(strict_types=1);

namespace App\Services\WordGenerator;

/**
 * Normalisasi payload agar menerima bentuk PoC (kontrak backend) maupun
 * variasi field dari UI/adaptor (nomor_surat/atas, dsb) secara defensif.
 */
final class PayloadNormalizer
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public static function normalize(array $payload): array
    {
        $out = $payload;

        $common = $out['informasi_bersama'] ?? [];
        if (! is_array($common)) {
            $common = [];
        }
        $tembusan = $common['tembusan'] ?? [];
        if (is_string($tembusan)) {
            $tembusan = preg_split('/\r?\n/', $tembusan) ?: [];
        }
        if (is_array($tembusan)) {
            $clean = [];
            foreach ($tembusan as $item) {
                if (is_string($item)) {
                    $t = trim($item);
                    if ($t !== '') {
                        $clean[] = $t;
                    }
                }
            }
            $common['tembusan'] = array_values($clean);
        }
        // Normalisasi nama field opsional
        if (isset($common['tanggal_surat']) && ! is_string($common['tanggal_surat'])) {
            $common['tanggal_surat'] = (string) $common['tanggal_surat'];
        }
        $out['informasi_bersama'] = $common;

        $groups = $out['kelompok_audit'] ?? [];
        if (! is_array($groups)) {
            $groups = [];
        }
        $normalizedGroups = [];
        $nameToId = [];
        foreach ($groups as $idx => $g) {
            if (! is_array($g)) {
                continue;
            }
            $id = $g['id'] ?? ($g['group_id'] ?? ($idx + 1));
            $id = is_string($id) || is_int($id) ? (string) $id : (string) ($idx + 1);
            $name = isset($g['nama']) && is_string($g['nama']) ? trim($g['nama']) : (isset($g['name']) && is_string($g['name']) ? trim($g['name']) : $id);
            $refs = $g['referensi'] ?? ($g['references'] ?? []);
            if (! is_array($refs)) {
                $refs = [];
            }
            $refList = [];
            foreach ($refs as $r) {
                if (is_string($r)) {
                    $refList[] = $r;
                }
            }
            $ng = $g;
            $ng['id'] = $id;
            $ng['nama'] = $name;
            $ng['referensi'] = array_values($refList);
            $normalizedGroups[] = $ng;
            if ($name !== '') {
                $nameToId[mb_strtolower($name)] = $id;
            }
            $nameToId[mb_strtolower($id)] = $id;
        }
        $out['kelompok_audit'] = $normalizedGroups;

        $recipients = $out['penerima'] ?? [];
        if (! is_array($recipients)) {
            $recipients = [];
        }
        $normalizedRecipients = [];
        foreach ($recipients as $r) {
            if (! is_array($r)) {
                continue;
            }
            $nr = $r;
            // Aliases dari UI v4 / adaptor
            if (! isset($nr['nomor_memorandum']) && isset($nr['nomor_surat'])) {
                $nr['nomor_memorandum'] = $nr['nomor_surat'];
            }
            if (! isset($nr['alasan_teguran']) && isset($nr['atas'])) {
                $nr['alasan_teguran'] = $nr['atas'];
            }
            if (! isset($nr['pangkat_golongan']) && isset($nr['pangkat_gol'])) {
                $nr['pangkat_golongan'] = $nr['pangkat_gol'];
            }
            // NIP harus string agar nol depan aman
            if (array_key_exists('nip', $nr) && $nr['nip'] !== null && ! is_string($nr['nip'])) {
                $nr['nip'] = (string) $nr['nip'];
            }
            // Kelompok audit: izinkan nama yang cocok ke id
            $gid = $nr['kelompok_audit_id'] ?? ($nr['audit_group_id'] ?? null);
            if ($gid !== null && ! is_string($gid) && ! is_int($gid)) {
                $gid = null;
            }
            $gidStr = $gid !== null ? trim((string) $gid) : '';
            if ($gidStr !== '' && isset($nameToId[mb_strtolower($gidStr)])) {
                $nr['kelompok_audit_id'] = $nameToId[mb_strtolower($gidStr)];
            } elseif ($gidStr !== '') {
                $nr['kelompok_audit_id'] = $gidStr;
            }

            // referensi_khusus: string/baris -> array; kosong -> null
            $custom = $nr['referensi_khusus'] ?? null;
            if (is_string($custom)) {
                $lines = preg_split('/\r?\n/', $custom) ?: [];
                $list = [];
                foreach ($lines as $line) {
                    $t = trim($line);
                    if ($t !== '') {
                        $list[] = $t;
                    }
                }
                $nr['referensi_khusus'] = count($list) ? array_values($list) : null;
            } elseif (is_array($custom)) {
                $list = [];
                foreach ($custom as $line) {
                    if (is_string($line)) {
                        $t = trim($line);
                        if ($t !== '') {
                            $list[] = $t;
                        }
                    }
                }
                $nr['referensi_khusus'] = count($list) ? array_values($list) : null;
            }

            $normalizedRecipients[] = $nr;
        }
        $out['penerima'] = $normalizedRecipients;

        return $out;
    }
}
