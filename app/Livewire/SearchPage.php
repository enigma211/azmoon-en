<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\Exam;

class SearchPage extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $query = '';

    public function search()
    {
        $this->resetPage();
    }

    public function updatedQuery()
    {
        $this->resetPage();
    }

    public function paginationView()
    {
        return 'livewire.custom-pagination';
    }

    public function render()
    {
        $results = collect();

        if (strlen($this->query) >= 2) {
            $searchQuery = trim($this->query);
            
            $results = Exam::query()
                ->where('is_published', true)
                ->where(function($q) use ($searchQuery) {
                    $q->where('title', 'LIKE', '%' . $searchQuery . '%')
                      ->orWhere('description', 'LIKE', '%' . $searchQuery . '%');
                })
                ->with(['batch.domain'])
                ->orderBy('sort_order')
                ->paginate(10);
        }

        return view('livewire.search-page', [
            'results' => $results
        ])->layout('layouts.app', [
            'seoTitle' => 'Search Exams - ExamApp',
            'seoDescription' => 'Search for exams by title',
        ]);
    }
}
