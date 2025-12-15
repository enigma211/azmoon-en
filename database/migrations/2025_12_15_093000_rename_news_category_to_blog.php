<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $category = Category::where('slug', 'news')->first();
        if ($category) {
            $category->update([
                'title' => 'Blog',
                'slug' => 'blog'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $category = Category::where('slug', 'blog')->first();
        if ($category) {
            $category->update([
                'title' => 'News',
                'slug' => 'news'
            ]);
        }
    }
};
