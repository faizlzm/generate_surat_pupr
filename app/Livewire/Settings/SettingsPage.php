<?php

namespace App\Livewire\Settings;

use App\Models\AppSetting;
use App\Models\AuditReferenceGroup;
use App\Models\Employee;
use App\Services\AuditReferenceFormatter;
use Livewire\Component;

class SettingsPage extends Component
{
    public string $tab = 'general';

    // general
    public string $ministry = '';
    public string $directorate = '';
    public string $address = '';
    public string $sender_title = '';
    public string $subject = 'Teguran Tertulis';
    public string $signatory_title = '';
    public string $signatory_name = '';
    public string $instruction_template = '';
    /** @var array<int,string> */
    public array $cc_list = [''];

    // employee form
    public ?int $editing_employee_id = null;
    public string $emp_name = '';
    public string $emp_nip = '';
    public string $emp_rank = '';
    public string $emp_grade = '';
    public string $emp_position = '';

    // group form
    public ?int $editing_group_id = null;
    public string $group_name = '';
    public string $group_description = '';
    /** @var array<int,string> */
    public array $group_references = [''];

    public function mount(): void
    {
        $this->loadGeneral();
    }

    public function loadGeneral(): void
    {
        $s = AppSetting::defaults();
        $this->ministry = (string) ($s['ministry'] ?? '');
        $this->directorate = (string) ($s['directorate'] ?? '');
        $this->address = (string) ($s['address'] ?? '');
        $this->sender_title = (string) ($s['sender_title'] ?? '');
        $this->subject = (string) ($s['subject'] ?? 'Teguran Tertulis');
        $this->signatory_title = (string) ($s['signatory_title'] ?? '');
        $this->signatory_name = (string) ($s['signatory_name'] ?? '');
        $this->instruction_template = (string) ($s['instruction_template'] ?? '');
        $cc = $s['cc_list'] ?? [];
        $this->cc_list = is_array($cc) && count($cc) ? array_values(array_map('strval', $cc)) : [''];
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function saveGeneral(): void
    {
        $cc = array_values(array_filter(array_map(static fn ($v) => trim((string) $v), $this->cc_list), static fn ($v) => $v !== ''));
        AppSetting::saveDefaults([
            'ministry' => trim($this->ministry),
            'directorate' => trim($this->directorate),
            'address' => trim($this->address),
            'sender_title' => trim($this->sender_title),
            'subject' => trim($this->subject) !== '' ? trim($this->subject) : 'Teguran Tertulis',
            'signatory_title' => trim($this->signatory_title),
            'signatory_name' => trim($this->signatory_name),
            'instruction_template' => trim($this->instruction_template),
            'cc_list' => $cc,
        ]);
        session()->flash('success', 'Pengaturan berhasil disimpan.');
        $this->loadGeneral();
    }

    public function addCc(): void { $this->cc_list[] = ''; }
    public function removeCc(int $i): void
    {
        if (count($this->cc_list) <= 1) { $this->cc_list = ['']; return; }
        unset($this->cc_list[$i]);
        $this->cc_list = array_values($this->cc_list);
    }

    // Employees
    public function editEmployee(int $id): void
    {
        $e = Employee::query()->findOrFail($id);
        $this->editing_employee_id = $e->id;
        $this->emp_name = (string) $e->name;
        $this->emp_nip = (string) ($e->nip ?? '');
        $this->emp_rank = (string) ($e->rank ?? '');
        $this->emp_grade = (string) ($e->grade ?? '');
        $this->emp_position = (string) ($e->position ?? '');
    }

    public function resetEmployeeForm(): void
    {
        $this->editing_employee_id = null;
        $this->emp_name = $this->emp_nip = $this->emp_rank = $this->emp_grade = $this->emp_position = '';
    }

    public function saveEmployee(): void
    {
        $data = $this->validate([
            'emp_name' => 'required|string|max:255',
            'emp_nip' => 'nullable|string|max:30',
            'emp_rank' => 'nullable|string|max:100',
            'emp_grade' => 'nullable|string|max:50',
            'emp_position' => 'nullable|string|max:255',
        ], [], [
            'emp_name' => 'nama pegawai',
            'emp_nip' => 'NIP',
        ]);
        $payload = [
            'name' => trim($data['emp_name']),
            'nip' => trim((string) ($data['emp_nip'] ?? '')) ?: null,
            'rank' => trim((string) ($data['emp_rank'] ?? '')) ?: null,
            'grade' => trim((string) ($data['emp_grade'] ?? '')) ?: null,
            'position' => trim((string) ($data['emp_position'] ?? '')) ?: null,
        ];
        if ($this->editing_employee_id) {
            Employee::query()->whereKey($this->editing_employee_id)->update($payload);
            session()->flash('success', 'Data pegawai diperbarui.');
        } else {
            Employee::query()->create($payload);
            session()->flash('success', 'Data pegawai ditambahkan.');
        }
        $this->resetEmployeeForm();
    }

    public function deleteEmployee(int $id): void
    {
        Employee::query()->whereKey($id)->delete();
        session()->flash('success', 'Data pegawai dihapus.');
        if ($this->editing_employee_id === $id) $this->resetEmployeeForm();
    }

    // Groups
    public function editGroup(int $id): void
    {
        $g = AuditReferenceGroup::query()->findOrFail($id);
        $this->editing_group_id = $g->id;
        $this->group_name = (string) $g->name;
        $this->group_description = (string) ($g->description ?? '');
        $refs = $g->references();
        $this->group_references = count($refs) ? $refs : [''];
    }

    public function resetGroupForm(): void
    {
        $this->editing_group_id = null;
        $this->group_name = $this->group_description = '';
        $this->group_references = [''];
    }

    public function addGroupRef(): void { $this->group_references[] = ''; }
    public function removeGroupRef(int $i): void
    {
        if (count($this->group_references) <= 1) { $this->group_references = ['']; return; }
        unset($this->group_references[$i]);
        $this->group_references = array_values($this->group_references);
    }
    public function moveGroupRef(int $index, string $direction): void
    {
        $j = $direction === 'up' ? $index - 1 : $index + 1;
        if ($j < 0 || $j >= count($this->group_references)) return;
        [$this->group_references[$index], $this->group_references[$j]] = [$this->group_references[$j], $this->group_references[$index]];
    }

    public function saveGroup(AuditReferenceFormatter $formatter): void
    {
        $this->validate([
            'group_name' => 'required|string|max:255',
            'group_description' => 'nullable|string|max:1000',
        ], [], ['group_name' => 'nama kelompok']);
        $refs = $formatter->normalizedTexts($this->group_references);
        $payload = [
            'name' => trim($this->group_name),
            'description' => trim($this->group_description) ?: null,
            'references_json' => $refs,
        ];
        if ($this->editing_group_id) {
            AuditReferenceGroup::query()->whereKey($this->editing_group_id)->update($payload);
            session()->flash('success', 'Kelompok referensi diperbarui.');
        } else {
            AuditReferenceGroup::query()->create($payload);
            session()->flash('success', 'Kelompok referensi ditambahkan.');
        }
        $this->resetGroupForm();
    }

    public function deleteGroup(int $id): void
    {
        AuditReferenceGroup::query()->whereKey($id)->delete();
        session()->flash('success', 'Kelompok referensi dihapus.');
        if ($this->editing_group_id === $id) $this->resetGroupForm();
    }

    public function render(AuditReferenceFormatter $formatter)
    {
        return view('livewire.settings.settings-page', [
            'employees' => Employee::query()->orderBy('name')->get(),
            'groups' => AuditReferenceGroup::query()->orderBy('name')->get(),
            'groupFormattedPreview' => $formatter->format($this->group_references),
        ])->layout('layouts.app')->title('Pengaturan');
    }
}
