<?php

namespace App\Livewire;

use App\Models\Letter;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard', [
            'total' => Letter::query()->count(),
            'drafts' => Letter::query()->where('status', Letter::STATUS_DRAFT)->count(),
            'finals' => Letter::query()->where('status', Letter::STATUS_FINAL)->count(),
            'recent' => Letter::query()->latest()->limit(5)->get(),
        ])->layout('layouts.app')->title('Dashboard');
    }
}
