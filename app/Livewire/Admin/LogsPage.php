<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class LogsPage extends Component
{
    use WithPagination;

    #[Url]
    public string $q = '';

    #[Url]
    public string $event = 'all'; // all | exam_started | exam_finished | result_viewed | result_viewed_session

    public function updatingQ(): void { $this->resetPage(); }
    public function updatingEvent(): void { $this->resetPage(); }

    public function render()
    {
        $query = ActivityLog::query()->with(['user', 'exam', 'attempt'])->orderByDesc('id');

        if ($this->q !== '') {
            $q = trim($this->q);
            $query->where(function ($qq) use ($q) {
                $qq->where('user_agent', 'like', "%{$q}%")
                   ->orWhere('ip', 'like', "%{$q}%")
                   ->orWhereHas('exam', function ($e) use ($q) { $e->where('title', 'like', "%{$q}%"); });
            });
        }
        if ($this->event !== 'all') {
            $query->where('event', $this->event);
        }

        $logs = $query->paginate(20);

        return view('livewire.admin.logs-page', [
            'logs' => $logs,
        ])->layout('layouts.app');
    }
}
