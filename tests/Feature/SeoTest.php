<?php

use function Pest\Laravel\get;

test('main public pages are accessible (HTTP 200)', function () {
    $pages = [
        route('home'),
        route('blog.index'),
        route('domains'),
    ];

    foreach ($pages as $page) {
        get($page)->assertStatus(200);
    }
});

test('critical seo files are accessible', function () {
    // robots.txt is a static file, so we check its existence on disk
    $this->assertTrue(file_exists(public_path('robots.txt')), 'robots.txt file missing');

    // Sitemap is a route handled by controller, so we verify HTTP 200
    $response = get(route('sitemap'));
    $response->assertStatus(200);

    // Verify static pages are in the sitemap
    $response->assertSee(route('about'));
    $response->assertSee(route('terms'));
    $response->assertSee(route('privacy-policy'));
});

test('informational pages are accessible and indexable', function () {
    $infoPages = [
        route('about'),
        route('terms'),
        route('privacy-policy'),
    ];

    foreach ($infoPages as $page) {
        $response = get($page);
        $response->assertStatus(200);
        $response->assertDontSee('noindex'); // Ensure they are not blocked by accidental meta tag
    }
});

test('homepage has critical seo tags', function () {
    $response = get(route('home'));

    $response->assertStatus(200);

    // Check for <title>
    $response->assertSee('<title>', false);

    // Check for Meta Description
    $response->assertSee('name="description"', false);

    // Check for H1 tag
    $response->assertSee('<h1', false);
});

test('blog page has critical seo tags', function () {
    $response = get(route('blog.index'));

    $response->assertStatus(200);
    $response->assertSee('<title>', false);
    $response->assertSee('name="description"', false);
    $response->assertSee('<h1', false);
});

test('private pages are protected from guests (Redirects)', function () {
    // Guests trying to access attempts should be redirected to login
    get(route('attempts'))
        ->assertStatus(302)
        ->assertRedirect(route('login'));
});

test('pages have correct language attribute', function () {
    get(route('home'))->assertSee('lang="', false);
});

test('pages have canonical tags', function () {
    get(route('home'))->assertSee('rel="canonical"', false);
});

test('pages have open graph social tags', function () {
    $this->get('/')
        ->assertSee('<meta property="og:title"', false)
        ->assertSee('<meta property="og:description"', false)
        ->assertSee('<meta property="og:image"', false);
});

test('sensitive files are protected', function () {
    $this->get('/.env')->assertStatus(404);
});
