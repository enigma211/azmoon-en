<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Slider;
use App\Models\ExamDomain;
use App\Models\Exam;
use App\Models\SupportTicket;
use App\Livewire\HomePage;
use App\Livewire\SearchPage;
use App\Livewire\SupportTicketsPage;
use Livewire\Livewire;
use function Pest\Laravel\actingAs;

test('home page renders sliders and domains', function () {
    $slider = Slider::create([
        'title' => 'Test Slider',
        'image' => 'slider.jpg',
        'is_active' => true,
        'order' => 1
    ]);
    $domain = ExamDomain::create([
        'title' => 'Test Domain',
        'slug' => 'test-domain',
        'is_active' => true,
        'image' => 'domain.jpg'
    ]);

    Livewire::test(HomePage::class)
        ->assertStatus(200)
        ->assertViewHas('sliders')
        ->assertViewHas('domains')
        ->assertSee('Test Domain');
});

test('search page returns relevant results', function () {
    $exam = Exam::factory()->create(['title' => 'Special Question Bank', 'is_published' => true]);

    Livewire::test(SearchPage::class)
        ->set('query', 'Special')
        ->assertViewHas('results', function ($results) use ($exam) {
            return $results->total() > 0;
        })
        ->assertSee('Special');
});

test('support tickets page handles submission and validation', function () {
    $user = User::factory()->create();
    actingAs($user);

    // 1. Validation test
    Livewire::test(SupportTicketsPage::class)
        ->call('createTicket')
        ->assertHasErrors(['subject', 'message']);

    // 2. Successful submission
    Livewire::test(SupportTicketsPage::class)
        ->set('subject', 'Help me')
        ->set('message', 'I have a problem with my exam.')
        ->call('createTicket')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('support_tickets', [
        'user_id' => $user->id,
        'subject' => 'Help me',
    ]);
});
