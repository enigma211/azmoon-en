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
    // because Feature tests mock the router and don't serve static files directly.
    $this->assertTrue(file_exists(public_path('robots.txt')), 'robots.txt file missing');

    // Sitemap is a route handled by controller, so we verify HTTP 200
    get(route('sitemap'))->assertStatus(200);
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
    // Guests trying to access profile should be redirected to login
    get(route('profile'))
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
