<?php

namespace App\Filament\Resources\ExamDomainResource\Pages;

use App\Filament\Resources\ExamDomainResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\ExamBatch;
use Illuminate\Support\Str;

class EditExamDomain extends EditRecord
{
    protected static string $resource = ExamDomainResource::class;

    protected bool $shouldGenerateStates = false;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Use the data argument or fallback to component state
        $this->shouldGenerateStates = $data['generate_us_states'] ?? $this->data['generate_us_states'] ?? false;
        
        // Remove from data to prevent model fill error (although dehydrated=false should handle this, better safe)
        unset($data['generate_us_states']);
        
        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->shouldGenerateStates) {
            $states = [
                'Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 
                'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 
                'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 
                'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 
                'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'
            ];

            // Get current max sort order once to avoid N+1 queries
            $currentMaxSort = $this->record->batches()->max('sort_order') ?? 0;

            foreach ($states as $state) {
                // Generate base slug: state-domain_slug
                $baseSlug = Str::slug($state . '-' . $this->record->slug);
                
                // Check if we already have a batch for this state in this domain
                // We check by title or if the slug matches our expected pattern
                $exists = ExamBatch::where('exam_domain_id', $this->record->id)
                    ->where(function($query) use ($state, $baseSlug) {
                        $query->where('title', $state)
                              ->orWhere('slug', 'like', $baseSlug . '%');
                    })
                    ->exists();

                if (!$exists) {
                    $currentMaxSort++;
                    
                    // Ensure slug uniqueness globally
                    $slug = $baseSlug;
                    $originalSlug = $slug;
                    $counter = 1;
                    while (ExamBatch::where('slug', $slug)->exists()) {
                        $slug = $originalSlug . '-' . $counter;
                        $counter++;
                    }

                    // Use Domain SEO Title as the base suffix, or fallback to a default
                    $baseSeoTitle = $this->record->seo_title ?? "Practice Test [year]";

                    ExamBatch::create([
                        'exam_domain_id' => $this->record->id,
                        'title' => $state,
                        'slug' => $slug,
                        'is_active' => true,
                        'sort_order' => $currentMaxSort,
                        'seo_title' => "$state $baseSeoTitle",
                        'seo_description' => "Free $state practice tests and exam questions for [year].",
                    ]);
                }
            }
        }
    }
}
