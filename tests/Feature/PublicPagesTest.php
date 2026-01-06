<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Category;
use Livewire\Livewire;
use App\Livewire\HomePage;
use App\Livewire\BlogPage;
use App\Livewire\BlogPostPage;

test('homepage renders successfully with livewire', function () {
    Livewire::test(HomePage::class)
        ->assertStatus(200);
});

test('blog page renders successfully with livewire', function () {
    Livewire::test(BlogPage::class)
        ->assertStatus(200);
});

test('blog detail page (blog.show) displays a post', function () {
    // Create pre-requisites
    $category = Category::create([
        'title' => 'Sample Category',
        'slug' => 'sample-cat',
    ]);

    $post = Post::create([
        'category_id' => $category->id,
        'title' => 'Test Post',
        'slug' => 'test-post',
        'content' => 'This is a test content',
        'is_published' => true,
        'published_at' => now(),
    ]);

    // Format matches: blog/{category}/{slug}
    $this->get(route('blog.show', ['category' => $category->slug, 'slug' => $post->slug]))
        ->assertStatus(200)
        ->assertSee('Test Post')
        ->assertSee('This is a test content');
});
