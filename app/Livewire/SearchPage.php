<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\Question;
use App\Models\ExamDomain;
use Illuminate\Database\Eloquent\Builder;

class SearchPage extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $query = '';

    #[Url]
    public $domain = '';

    public function search()
    {
        // Reset pagination when searching
        $this->resetPage();
    }

    public function paginationView()
    {
        return 'livewire.custom-pagination';
    }

    public function render()
    {
        $domains = ExamDomain::where('is_active', true)->get();
        
        $results = collect();

        if (strlen($this->query) >= 2) {
            $q = Question::query()
                ->where('is_deleted', false)
                ->with(['exam.batch.domain']);

            // Filter by domain if selected
            if ($this->domain) {
                $q->whereHas('exam.batch', function (Builder $b) {
                    $b->where('exam_domain_id', $this->domain);
                });
            }

            // Search logic: Handle Persian text with ZWNJ (نیم‌فاصله) properly
            // For multi-word queries like "پی سازی", we need to find them as a phrase
            // not as separate words scattered in the text
            
            $searchQuery = trim($this->query);
            
            // Build multiple LIKE patterns to handle:
            // 1. Exact match with space: "پی سازی"
            // 2. Match with ZWNJ (half-space): "پی‌سازی" 
            // 3. Match without any separator: "پیسازی"
            $q->where(function($subQ) use ($searchQuery) {
                // Pattern 1: Exact query as-is
                $subQ->where('text', 'LIKE', '%' . $searchQuery . '%');
                
                // Pattern 2: Replace spaces with ZWNJ character (U+200C)
                $withZwnj = str_replace(' ', "\u{200C}", $searchQuery);
                if ($withZwnj !== $searchQuery) {
                    $subQ->orWhere('text', 'LIKE', '%' . $withZwnj . '%');
                }
                
                // Pattern 3: Remove all spaces (words joined together)
                $noSpaces = str_replace(' ', '', $searchQuery);
                if ($noSpaces !== $searchQuery) {
                    $subQ->orWhere('text', 'LIKE', '%' . $noSpaces . '%');
                }
            });

            // Order by latest
            $results = $q->latest()->paginate(10);
        }

        return view('livewire.search-page', [
            'domains' => $domains,
            'results' => $results
        ])->layout('layouts.app', [
            'seoTitle' => 'جستجوی سوالات - آزمون کده',
            'seoDescription' => 'جستجوی پیشرفته در بانک سوالات آزمون‌های نظام مهندسی و کارشناس رسمی',
        ]);
    }
}
