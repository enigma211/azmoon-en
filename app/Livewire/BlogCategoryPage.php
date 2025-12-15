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
            'seoTitle' => 'News ' . $this->currentCategory->title . ' - AllExam24',
            'seoDescription' => 'Archive of news and articles related to ' . $this->currentCategory->title
        ]);
    }
}
