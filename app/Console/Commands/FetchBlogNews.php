<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Exception;

class FetchBlogNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news from RSS feeds and generate blog posts using AI';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fetch and process news...');

        // Fetch settings
        $apiKey = Setting::where('key', 'avalai_api_key')->value('value');
        $baseUrl = Setting::where('key', 'avalai_base_url')->value('value') ?? 'https://api.avalai.ir/v1';
        $prompt = Setting::where('key', 'autopilot_prompt')->value('value');
        $categoryId = Setting::where('key', 'autopilot_category_id')->value('value');
        $rssFeedsText = Setting::where('key', 'autopilot_rss_feeds')->value('value');
        $minPosts = (int)(Setting::where('key', 'autopilot_min_posts_per_day')->value('value') ?? 1);
        $maxPosts = (int)(Setting::where('key', 'autopilot_max_posts_per_day')->value('value') ?? 3);

        if (!$apiKey || !$prompt || !$categoryId || !$rssFeedsText) {
            $this->error('Missing required settings. Please configure Autopilot Settings in the admin panel.');
            return;
        }

        // Check how many auto-generated posts were already created today
        $todayPostsCount = Post::whereNotNull('source_url')
            ->whereDate('created_at', now()->toDateString())
            ->count();

        if ($todayPostsCount >= $maxPosts) {
            $this->info("Daily limit reached ({$maxPosts} posts). Skipping fetch.");
            return;
        }

        $postsNeeded = max($minPosts - $todayPostsCount, 1);
        $this->info("Targeting to fetch {$postsNeeded} new posts (Max allowed: {$maxPosts}, Created today: {$todayPostsCount}).");

        $feeds = array_filter(array_map('trim', explode("\n", $rssFeedsText)));
        
        $postsCreatedThisRun = 0;

        foreach ($feeds as $feedUrl) {
            if ($todayPostsCount + $postsCreatedThisRun >= $maxPosts) {
                $this->info("Maximum daily post limit ({$maxPosts}) reached during this run. Stopping.");
                break;
            }
            $this->info("Fetching RSS from: {$feedUrl}");
            
            try {
                $response = Http::get($feedUrl);
                if (!$response->successful()) {
                    $this->warn("Failed to fetch {$feedUrl}");
                    continue;
                }

                $xml = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
                if (!$xml || !isset($xml->channel->item)) {
                    $this->warn("Invalid RSS format for {$feedUrl}");
                    continue;
                }

                $items = $xml->channel->item;
                $processedCount = 0;

                foreach ($items as $item) {
                    if ($todayPostsCount + $postsCreatedThisRun >= $maxPosts) {
                        break 2; // Break out of both loops if overall max is reached
                    }

                    // Only process up to max needed per feed to avoid rate limits
                    if ($processedCount >= $postsNeeded) {
                        break;
                    }

                    $sourceUrl = (string) $item->link;
                    
                    // Check if already processed
                    if (Post::where('source_url', $sourceUrl)->exists()) {
                        continue;
                    }

                    $title = (string) $item->title;
                    $description = (string) $item->description;
                    $content = isset($item->children('content', true)->encoded) 
                        ? (string) $item->children('content', true)->encoded 
                        : $description;

                    // Clean tags for the prompt
                    $cleanContent = strip_tags($content);
                    $originalText = "Title: {$title}\n\nContent: {$cleanContent}";

                    $this->info("Processing new article: {$title}");

                    $aiResult = $this->rewriteWithAI($apiKey, $baseUrl, $prompt, $originalText);

                    if ($aiResult && isset($aiResult['title']) && isset($aiResult['content'])) {
                        Post::create([
                            'title' => $aiResult['title'],
                            'slug' => Str::slug($aiResult['title']) . '-' . uniqid(),
                            'summary' => $aiResult['summary'] ?? Str::limit(strip_tags($aiResult['content']), 150),
                            'content' => $aiResult['content'],
                            'category_id' => $categoryId,
                            'is_published' => true,
                            'published_at' => now(),
                            'source_url' => $sourceUrl,
                        ]);
                        $this->info("Successfully created post: {$aiResult['title']}");
                        $processedCount++;
                        $postsCreatedThisRun++;
                    } else {
                        $this->warn("Failed to generate AI content for: {$title}");
                    }

                    // Delay to prevent hitting rate limits
                    sleep(2);
                }

            } catch (Exception $e) {
                $this->error("Error processing {$feedUrl}: " . $e->getMessage());
            }
        }

        $this->info('News fetch completed.');
    }

    private function rewriteWithAI($apiKey, $baseUrl, $prompt, $content)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post(rtrim($baseUrl, '/') . '/chat/completions', [
            'model' => 'gpt-4o-mini', // or 'claude-3-haiku' depending on avalai availability
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant that rewrites articles. You MUST return your answer in valid JSON format only, with no markdown code blocks outside the JSON. Format: {"title": "string", "summary": "string", "content": "html string"}'],
                ['role' => 'user', 'content' => $prompt . "\n\n" . $content]
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.7,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $aiText = $data['choices'][0]['message']['content'] ?? '';
            
            // Clean up any potential markdown code blocks returned despite instructions
            $aiText = str_replace(['```json', '```'], '', $aiText);
            
            return json_decode(trim($aiText), true);
        }

        return null;
    }
}
