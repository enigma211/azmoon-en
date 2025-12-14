<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;
use Illuminate\Support\Str;

class BlogPostPage extends Component
{
    public $slug;
    public $category;
    public Post $post;

    public function mount($category, $slug)
    {
        $this->slug = $slug;
        $this->category = $category;
        
        $this->post = Post::published()->with('category')->where('slug', $slug)->firstOrFail();

        // SEO Check: If category slug in URL doesn't match post's category, redirect permanently
        if ($this->post->category && $this->post->category->slug !== $category) {
            return redirect()->route('blog.show', [
                'category' => $this->post->category->slug, 
                'slug' => $this->post->slug
            ], 301);
        }
        
        // Increment view count
        $this->post->increment('view_count');
    }

    public function render()
    {
        $recentPosts = Post::published()
            ->where('id', '!=', $this->post->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('livewire.blog-post-page', [
            'recentPosts' => $recentPosts
        ])->layout('layouts.app', [
            'seoTitle' => $this->post->title . ' - آزمون کده',
            'seoDescription' => $this->post->summary ?? Str::limit(strip_tags($this->post->content), 160),
            'seoKeywords' => $this->post->meta_keywords,
            'seoImage' => $this->post->image_path ? url(\Illuminate\Support\Facades\Storage::url($this->post->image_path)) : null,
        ]);
    }
}
