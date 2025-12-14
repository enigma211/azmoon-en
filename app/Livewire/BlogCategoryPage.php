<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;

class BlogCategoryPage extends Component
{
    use WithPagination;

    public $categorySlug;
    public Category $currentCategory;

    public function mount($category)
    {
        $this->categorySlug = $category;
        $this->currentCategory = Category::where('slug', $category)->firstOrFail();
    }

    public function render()
    {
        $categories = Category::all();
        $posts = $this->currentCategory->posts()
            ->published()
            ->with('category')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return view('livewire.blog-category-page', [
            'posts' => $posts,
            'categories' => $categories
        ])->layout('layouts.app', [
            'seoTitle' => 'اخبار ' . $this->currentCategory->title . ' - آزمون کده',
            'seoDescription' => 'آرشیو اخبار و مقالات مربوط به ' . $this->currentCategory->title
        ]);
    }
}
