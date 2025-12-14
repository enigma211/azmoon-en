<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;

class BlogTagPage extends Component
{
    use WithPagination;

    public $tag;

    public function mount($tag)
    {
        $this->tag = $tag;
    }

    public function render()
    {
        $categories = Category::all();
        $posts = Post::published()
            ->with('category')
            ->where('meta_keywords', 'LIKE', '%' . $this->tag . '%')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return view('livewire.blog-tag-page', [
            'posts' => $posts,
            'categories' => $categories
        ])->layout('layouts.app', [
            'seoTitle' => 'اخبار با برچسب ' . $this->tag . ' - آزمون کده',
            'seoDescription' => 'آرشیو اخبار و مقالات دارای برچسب ' . $this->tag
        ]);
    }
}
