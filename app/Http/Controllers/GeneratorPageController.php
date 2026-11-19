<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\AuditReferenceGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class GeneratorPageController extends Controller
{
    public function index(): View
    {
        return view('word-generator', [
            'generatorBoot' => $this->buildBootConfig(),
        ]);
    }

    public function saveSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sender' => ['nullable', 'string'],
            'subject' => ['nullable', 'string'],
            'signTitle' => ['nullable', 'string'],
            'signName' => ['nullable', 'string'],
            'cc' => ['nullable', 'string'],
            'ministry' => ['nullable', 'string'],
            'agency' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
        ]);

        $current = $this->storedSettings();
        $merged = array_merge($current, array_intersect_key($validated, $current));

        // Map UI keys to AppSetting::defaultGeneral keys and persist.
        $general = AppSetting::defaultGeneral();
        $general['sender_title'] = $merged['sender'] ?? $general['sender_title'];
        $general['subject'] = $merged['subject'] ?? $general['subject'];
        $general['signatory_title'] = $merged['signTitle'] ?? $general['signatory_title'];
        $general['signatory_name'] = $merged['signName'] ?? $general['signatory_name'];
        $general['ministry'] = $merged['ministry'] ?? $general['ministry'];
        $general['directorate'] = $merged['agency'] ?? $general['directorate'];
        $general['address'] = $merged['address'] ?? $general['address'];
        $cc = $merged['cc'] ?? '';
        $general['cc_list'] = $this->ccToList(is_string($cc) ? $cc : '');

        AppSetting::saveDefaults($general);

        return response()->json([
            'ok' => true,
            'settings' => $this->uiSettingsFromGeneral(AppSetting::defaults()),
        ]);
    }

    public function saveGroups(Request $request): JsonResponse
    {
        $data = $request->validate([
            'groups' => ['required', 'array'],
            'groups.*.id' => ['nullable'],
            'groups.*.name' => ['nullable', 'string'],
            'groups.*.references' => ['nullable', 'array'],
            'groups.*.references.*' => ['nullable', 'string'],
        ]);

        $groups = $data['groups'] ?? [];
        $normalized = [];
        foreach ($groups as $g) {
            $name = isset($g['name']) && is_string($g['name']) ? trim($g['name']) : '';
            $refs = [];
            foreach (($g['references'] ?? []) as $r) {
                $refs[] = is_string($r) ? $r : (string) $r;
            }
            // Keep at least one slot for UI consistency
            if ($refs === []) {
                $refs = [''];
            }
            $normalized[] = [
                'name' => $name,
                'references_json' => array_values($refs),
            ];
        }

        // Replace-all for simplicity (single-user, no drafts/history).
        DB::transaction(function () use ($normalized): void {
            AuditReferenceGroup::query()->delete();
            foreach ($normalized as $g) {
                AuditReferenceGroup::query()->create($g);
            }
        });

        $saved = $this->loadGroups();

        return response()->json([
            'ok' => true,
            'groups' => $saved,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildBootConfig(): array
    {
        $settings = $this->uiSettingsFromGeneral(AppSetting::defaults());
        $groups = $this->loadGroups();
        $useDefaults = $groups === [];

        return [
            'settings' => $settings,
            'groups' => $groups,
            // When no saved groups exist, let the UI fall back to its built-in sample groups/rows.
            'use_default_samples' => $useDefaults,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function uiSettingsFromGeneral(array $general): array
    {
        $cc = $general['cc_list'] ?? [];
        if (! is_array($cc)) {
            $cc = [];
        }
        $ccLines = implode("\n", array_values(array_filter(array_map(
            static fn ($v) => trim((string) $v),
            $cc
        ), static fn ($v) => $v !== '')));

        return [
            'ministry' => (string) ($general['ministry'] ?? ''),
            'agency' => (string) ($general['directorate'] ?? ($general['agency'] ?? '')),
            'address' => (string) ($general['address'] ?? ''),
            'signTitle' => (string) ($general['signatory_title'] ?? ''),
            'signName' => (string) ($general['signatory_name'] ?? ''),
            'cc' => $ccLines,
            // shared/common defaults
            'sender' => (string) ($general['sender_title'] ?? ''),
            'subject' => (string) ($general['subject'] ?? 'Teguran Tertulis'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function storedSettings(): array
    {
        return $this->uiSettingsFromGeneral(AppSetting::defaults());
    }

    /**
     * @return array<int, array{id:string,name:string,references:array<int,string>}>
     */
    private function loadGroups(): array
    {
        if (! Schema::hasTable('audit_reference_groups')) {
            return [];
        }

        $rows = AuditReferenceGroup::query()->orderBy('id')->get();
        $out = [];
        foreach ($rows as $row) {
            $refs = $row->references();
            if ($refs === []) {
                $refs = [''];
            }
            $out[] = [
                'id' => 'g'.$row->id,
                'name' => (string) ($row->name ?? ''),
                'references' => array_values(array_map(static fn ($v) => (string) $v, $refs)),
            ];
        }

        return $out;
    }

    /**
     * @return array<int, string>
     */
    private function ccToList(string $cc): array
    {
        $lines = preg_split('/\r?\n/', $cc) ?: [];
        $out = [];
        foreach ($lines as $line) {
            $t = trim((string) $line);
            if ($t !== '') {
                $out[] = $t;
            }
        }

        return $out;
    }
}
