<?php

namespace Cable8mm\DbToMarkdown\Test;

use Cable8mm\DbToMarkdown\Formats\Jekyll;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

final class JekyllTest extends TestCase
{
    public function test_it_can_do_well(): void
    {
        $jekyll = new Jekyll(
            layout: 'single',
            title: 'Sample Title',
            date: new Carbon,
            author: 'Samgu Lee',
            body: '## Heading'.PHP_EOL.PHP_EOL.'Contents',
            slug: '1'
        );

        $render = $jekyll->render();

        $this->assertNotNull($render);
    }

    public function test_it_must_use_camelcase_filename(): void
    {
        $now = new Carbon;

        $jekyll = new Jekyll(
            layout: 'single',
            title: 'Sample Title',
            date: $now,
            author: 'Samgu Lee',
            body: '## Heading'.PHP_EOL.PHP_EOL.'Contents',
            slug: '930'
        );

        $path = $jekyll->path();

        $this->assertEquals($now->format('Y-m-d-').'930.markdown', $path);
    }

    public function test_it_generates_yaml_front_matter(): void
    {
        $jekyll = new Jekyll(
            layout: 'post',
            title: 'My Post Title',
            date: new Carbon('2024-06-15 14:30:00'),
            author: 'John Doe',
            body: '# Post body content',
            slug: 'my-post'
        );

        $render = $jekyll->render();

        $this->assertStringStartsWith('---'.PHP_EOL, $render);
        $this->assertStringContainsString('layout: post', $render);
        $this->assertStringContainsString('title: "My Post Title"', $render);
        $this->assertStringContainsString('author: John Doe', $render);
        $this->assertStringEndsWith('---'.PHP_EOL.'# Post body content', $render);
    }

    public function test_it_includes_categories_in_front_matter(): void
    {
        $jekyll = new Jekyll(
            layout: 'single',
            title: 'Title',
            date: new Carbon('2024-06-15'),
            author: 'Author',
            body: 'Body',
            slug: 'slug',
            categories: 'tech'
        );

        $render = $jekyll->render();

        $this->assertStringContainsString('categories: tech', $render);
    }

    public function test_it_omits_categories_if_null(): void
    {
        $jekyll = new Jekyll(
            layout: 'single',
            title: 'Title',
            date: new Carbon('2024-06-15'),
            author: 'Author',
            body: 'Body',
            slug: 'slug',
            categories: null
        );

        $render = $jekyll->render();

        $this->assertStringNotContainsString('categories', $render);
    }

    public function test_path_replaces_special_characters_in_slug(): void
    {
        $now = new Carbon('2024-06-15');

        $jekyll = new Jekyll(
            layout: 'single',
            title: 'Title',
            date: $now,
            author: 'Author',
            body: 'Body',
            slug: 'hello_world test_slug'
        );

        $path = $jekyll->path();

        $this->assertEquals('2024-06-15-hello-world-test-slug.markdown', $path);
    }

    public function test_path_with_custom_extension(): void
    {
        $now = new Carbon('2024-06-15');

        $jekyll = new Jekyll(
            layout: 'single',
            title: 'Title',
            date: $now,
            author: 'Author',
            body: 'Body',
            slug: 'slug'
        );

        $path = $jekyll->path('md');

        $this->assertEquals('2024-06-15-slug.md', $path);
    }

    public function test_it_returns_default_extension(): void
    {
        $jekyll = new Jekyll(
            layout: 'single',
            title: 'Title',
            date: new Carbon('2024-06-15'),
            author: 'Author',
            body: 'Body',
            slug: 'slug'
        );

        $this->assertEquals('markdown', $jekyll->getExtension());
    }
}
