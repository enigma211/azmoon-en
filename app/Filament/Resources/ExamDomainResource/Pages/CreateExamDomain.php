<?php

namespace App\Filament\Resources\ExamDomainResource\Pages;

use App\Filament\Resources\ExamDomainResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\ExamBatch;
use Illuminate\Support\Str;

class CreateExamDomain extends CreateRecord
{
    protected static string $resource = ExamDomainResource::class;

    protected bool $shouldGenerateStates = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Use the data argument or fallback to component state
        $this->shouldGenerateStates = $data['generate_us_states'] ?? $this->data['generate_us_states'] ?? false;
        
        // Remove from data to prevent model fill error
        unset($data['generate_us_states']);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->shouldGenerateStates) {
            $states = [
                'Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 
                'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 
                'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 
                'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 
                'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'
            ];

            foreach ($states as $index => $state) {
                // Use Domain SEO Title as the base suffix, or fallback to a default
                $baseSeoTitle = $this->record->seo_title ?? "Practice Test [year]";

                // Generate unique slug: state-domain_slug
                $slug = Str::slug($state . '-' . $this->record->slug);
                $originalSlug = $slug;
                $counter = 1;
                while (ExamBatch::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }

                ExamBatch::create([
                    'exam_domain_id' => $this->record->id,
                    'title' => $state,
                    'slug' => $slug,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                    'seo_title' => "$state $baseSeoTitle",
                    'seo_description' => "Free $state practice tests and exam questions for [year].",
                ]);
            }
        }
    }
}
