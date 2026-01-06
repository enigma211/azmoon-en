<?php

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'die', 'print_r', 'exit'])
    ->not->toBeUsed();

arch('models should be standard')
    ->expect('App\Models')
    ->toBeClasses()
    ->toExtend(\Illuminate\Database\Eloquent\Model::class)
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

arch('all test files should have Test suffix')
    ->expect('Tests\Feature')
    ->toHaveSuffix('Test');

arch('all unit test files should have Test suffix')
    ->expect('Tests\Unit')
    ->toHaveSuffix('Test');
