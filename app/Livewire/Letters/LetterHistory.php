<?php

namespace App\Livewire\Letters;

use App\Models\Letter;
use App\Services\LetterService;
use Livewire\Component;
use Livewire\WithPagination;

class LetterHistory extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = 'all';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function duplicate(int $letterId, LetterService $service): void
    {
        $letter = Letter::query()->findOrFail($letterId);
        $copy = $service->duplicate($letter);
        session()->flash('success', 'Surat berhasil diduplikasi menjadi draft baru.');
        $this->redirectRoute('letters.edit', ['letter' => $copy->id], navigate: false);
    }

    public function render()
    {
        $query = Letter::query()->latest();

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        $search = trim($this->search);
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('letter_number', 'like', '%'.$search.'%')
                    ->orWhere('payload_json', 'like', '%'.$search.'%');
            });
        }

        return view('livewire.letters.letter-history', [
            'letters' => $query->paginate(15),
        ])->layout('layouts.app')->title('Riwayat Surat');
    }
}
