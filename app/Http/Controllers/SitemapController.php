<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamBatch;
use App\Models\ExamDomain;
use App\Models\ExamType;
use App\Models\Post as BlogPost;
use App\Models\Category as BlogCategory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $xml = Cache::remember('sitemap.xml', 600, function () {
            $urls = [];

            // Static Pages
            $urls[] = [
                'loc' => route('home'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ];

            $urls[] = [
                'loc' => route('domains'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];

            // Flashcards Main Page
            $urls[] = [
                'loc' => route('flashcards.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];

            // Info Pages (About, Terms, Privacy)
            $urls[] = [
                'loc' => route('about'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ];

            $urls[] = [
                'loc' => route('terms'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ];

            $urls[] = [
                'loc' => route('privacy-policy'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ];

            $domains = ExamDomain::query()->orderBy('id','asc')->get(['id','updated_at']);
            foreach ($domains as $domain) {
                $urls[] = [
                    'loc' => route('batches', $domain),
                    'lastmod' => optional($domain->updated_at)->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ];
            }

            $batches = ExamBatch::query()->orderBy('id','asc')->get(['id','updated_at']);
            foreach ($batches as $batch) {
                $urls[] = [
                    'loc' => route('exams', $batch),
                    'lastmod' => optional($batch->updated_at)->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                ];
            }

            $exams = Exam::query()
                ->withCount(['questions' => function ($q) { $q->where('is_deleted', false); }])
                ->orderBy('id','asc')
                ->get(['id','updated_at']);

            foreach ($exams as $exam) {
                // Main Landing Page
                $urls[] = [
                    'loc' => route('exam.landing', ['exam' => $exam->id]),
                    'lastmod' => optional($exam->updated_at)->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.9',
                ];

                // Exam Play Page
                if ($exam->questions_count > 0) {
                    $urls[] = [
                        'loc' => route('exam.play', ['exam' => $exam->id]),
                        'lastmod' => optional($exam->updated_at)->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.8',
                    ];
                }
            }

            // Blog Main Page
            $urls[] = [
                'loc' => route('blog.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ];

            // Blog Categories
            $blogCategories = BlogCategory::all();
            foreach ($blogCategories as $category) {
                 $urls[] = [
                    'loc' => route('blog.category', $category->slug),
                    'lastmod' => now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ];
            }

            // Blog Posts
            $blogPosts = BlogPost::published()->orderBy('updated_at', 'desc')->get();
            foreach ($blogPosts as $post) {
                $categorySlug = $post->category ? $post->category->slug : 'general';
                 $urls[] = [
                    'loc' => route('blog.show', ['category' => $categorySlug, 'slug' => $post->slug]),
                    'lastmod' => $post->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            }

            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            foreach ($urls as $u) {
                $xml .= '<url>';
                $xml .= '<loc>' . e($u['loc']) . '</loc>';
                if (!empty($u['lastmod'])) {
                    $xml .= '<lastmod>' . e($u['lastmod']) . '</lastmod>';
                }
                if (!empty($u['changefreq'])) {
                    $xml .= '<changefreq>' . e($u['changefreq']) . '</changefreq>';
                }
                if (!empty($u['priority'])) {
                    $xml .= '<priority>' . e($u['priority']) . '</priority>';
                }
                $xml .= '</url>';
            }
            $xml .= '</urlset>';

            return $xml;
        });

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
