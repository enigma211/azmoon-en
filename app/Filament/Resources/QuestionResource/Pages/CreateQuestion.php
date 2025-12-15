<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateQuestion extends CreateRecord
{
    protected static string $resource = QuestionResource::class;

    protected function beforeCreate(): void
    {
        $data = $this->form->getState();

        $type = $data['type'] ?? null;
        $choices = collect($data['choices'] ?? [])->filter(fn($c) => isset($c['text']) && $c['text'] !== '');
        $correctCount = $choices->where('is_correct', true)->count();

        if (in_array($type, ['single_choice','true_false'])) {
            if ($choices->count() < 2) {
                throw ValidationException::withMessages([
                    'choices' => 'Single choice/True-False questions require at least two options.',
                ]);
            }
            if ($correctCount !== 1) {
                throw ValidationException::withMessages([
                    'choices' => 'Single choice/True-False questions must have exactly one correct option.',
                ]);
            }
        }

        if ($type === 'multi_choice') {
            if ($choices->count() < 2) {
                throw ValidationException::withMessages([
                    'choices' => 'Multiple choice questions require at least two options.',
                ]);
            }
            if ($correctCount < 1) {
                throw ValidationException::withMessages([
                    'choices' => 'Multiple choice questions must have at least one correct option.',
                ]);
            }
        }
    }
}
