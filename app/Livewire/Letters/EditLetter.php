<?php

namespace App\Livewire\Letters;

use App\Models\Letter;
use Livewire\Attributes\Locked;

class EditLetter extends CreateLetter
{

    public function mount(?Letter $letter = null): void
    {
        if ($letter && $letter->isFinal()) {
            session()->flash('warning', 'Surat final tidak dapat diubah. Silakan duplikasi dari Riwayat untuk membuat draft baru.');
            $this->redirectRoute('letters.index', navigate: false);
            return;
        }
        parent::mount($letter);
    }

    public function render(\App\Services\AuditReferenceFormatter $formatter)
    {
        $formattedRefs = $formatter->format($this->daftar_referensi_audit);

        return view('livewire.letters.edit-letter', [
            'employees' => \App\Models\Employee::query()->orderBy('name')->get(),
            'groups' => \App\Models\AuditReferenceGroup::query()->orderBy('name')->get(),
            'formattedRefs' => $formattedRefs,
            'letter' => $this->letterId ? Letter::query()->find($this->letterId) : null,
        ])->layout('layouts.app')->title('Edit Draft');
    }
}
