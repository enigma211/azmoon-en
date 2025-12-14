<?php

namespace App\Livewire;

use App\Models\ExamAttempt;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AttemptsPage extends Component
{
    use WithPagination;

    public string $status = 'all'; // all|in_progress|submitted|abandoned

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = ExamAttempt::query()
            ->with(['exam'])
            ->withCount('answers')
            ->where('user_id', Auth::id())
            ->orderByDesc('started_at');

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        } else {
            // By default, exclude cancelled attempts (incomplete/abandoned)
            $query->where('status', '!=', 'cancelled');
        }

        $attempts = $query->paginate(10);

        return view('livewire.attempts-page', [
            'attempts' => $attempts,
        ])->layout('layouts.app');
    }
}
