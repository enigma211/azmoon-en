<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;

class BlogPage extends Component
{
    use WithPagination;

    public function render()
    {
        $categories = \App\Models\Category::all();
        $posts = Post::published()
            ->with('category')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return view('livewire.blog-page', [
            'posts' => $posts,
            'categories' => $categories
        ])->layout('layouts.app', [
            'seoTitle' => 'اخبار و مقالات - آزمون کده',
            'seoDescription' => 'جدیدترین اخبار، مقالات و اطلاعیه‌های مربوط به آزمون‌های نظام مهندسی و کارشناس رسمی دادگستری'
        ]);
    }
}
