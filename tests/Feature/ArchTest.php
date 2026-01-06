<?php

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'die', 'print_r'])
    ->not->toBeUsed();

arch('models should be standard')
    ->expect('App\Models')
    ->toBeClasses()
    ->toExtend(\Illuminate\Database\Eloquent\Model::class)
    // We ignore User because it extends Authenticatable, which eventually extends Model,
    // but sometimes static analysis tools get picky about direct inheritance.
    ->ignoring(\App\Models\User::class);

arch('controllers should have Controller suffix')
    ->expect('App\Http\Controllers')
    ->toHaveSuffix('Controller');

arch('avoid open streams')
    ->expect(['fopen', 'file_get_contents'])
    ->not->toBeUsed()
    ->ignoring([
        'App\Http\Controllers\SitemapController',
        'App\Filament\Pages\ImportQuestions'
    ]);
