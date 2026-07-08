<?php

namespace Cable8mm\DbToMarkdown\Test;

use Cable8mm\DbToMarkdown\Models\Article;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

final class ArticleTest extends TestCase
{
    // Test-specific mapper using generic field names
    private const TEST_MAP = [
        'title' => 'title',
        'slug' => 'id',
        'categories' => 'category_id',
        'body' => 'content',
        'published_at' => 'published_at',
    ];

    public function test_callbacks_to_work_collectly(): void
    {
        $map = self::TEST_MAP;

        $html = '<p>[widget type="good" good="1000014584"]</p>'.PHP_EOL;
        $html .= '<p><img src="https://www.palgle.com/image.png" /></p>'.PHP_EOL;
        $html = '<p>[widget type="good" good="1000014584"]</p>'.PHP_EOL;
        $html .= '<p>only this to remain</p>';

        $row = [
            'title' => 'Test Title',
            'subtitle' => '',
            'id' => 1,
            'content' => $html,
            'category_id' => 1,
            'published_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)
            ->setBodyCallback(
                function ($item) {
                    return preg_replace('/<img[^>]+>/', '', $item);
                }, function ($item) {
                    return preg_replace('/\[[^\]]+\]\n?/u', '', $item);
                }
            )
            ->in($row);

        $this->assertEquals('only this to remain', $article->markdown());
    }

    public function test_regular_expression(): void
    {
        $in = '\[widget type="good" good="1000012875"\]'.PHP_EOL;
        $in .= '\[widget type="good" good="1000013673"\]'.PHP_EOL;
        $in .= '\[widget type="good" good="1000012875"\]'.PHP_EOL;
        $in .= '\[widget type="good" good="1000012875"\]';

        $actual = preg_replace('/\\\\\[[^\]]+\]\n?/', '', $in);

        $this->assertEmpty($actual);
    }

    public function test_title_is_string_when_map_title_is_string(): void
    {
        $map = self::TEST_MAP;

        $row = [
            'title' => 'Single Title',
            'subtitle' => '',
            'id' => 1,
            'content' => '<p>Body</p>',
            'category_id' => 1,
            'published_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)->in($row);

        $this->assertEquals('Single Title', $article->title);
    }

    public function test_title_concatenates_when_map_title_is_array(): void
    {
        $map = [
            'title' => ['title', 'subtitle'],
            'slug' => 'id',
            'categories' => 'category_id',
            'body' => 'content',
            'published_at' => 'published_at',
        ];

        $row = [
            'title' => 'Hello',
            'subtitle' => 'World',
            'id' => 1,
            'content' => '<p>Body content</p>',
            'category_id' => 1,
            'published_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)->in($row);

        $this->assertEquals('Hello World', $article->title);
    }

    public function test_slug_replaces_underscores_and_spaces(): void
    {
        $map = self::TEST_MAP;

        $row = [
            'title' => 'Title',
            'subtitle' => '',
            'id' => 100,
            'content' => '<p>Body</p>',
            'category_id' => 1,
            'published_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)->in($row);

        $this->assertStringContainsString('100', $article->slug);
    }

    public function test_categories_is_nullable(): void
    {
        $map = self::TEST_MAP;

        $row = [
            'title' => 'Title',
            'subtitle' => '',
            'id' => 1,
            'content' => '<p>Body</p>',
            'category_id' => null,
            'published_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)->in($row);

        $this->assertNull($article->categories);
    }

    public function test_resolve_categories_keeps_null_when_null(): void
    {
        $map = self::TEST_MAP;

        $row = [
            'title' => 'Title',
            'subtitle' => '',
            'id' => 1,
            'content' => '<p>Body</p>',
            'category_id' => null,
            'published_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)
            ->resolveCategories([1 => 'Action', 2 => 'Health'])
            ->in($row);

        $this->assertNull($article->categories);
    }

    public function test_resolve_categories_keeps_original_when_not_in_map(): void
    {
        $map = self::TEST_MAP;

        $row = [
            'title' => 'Title',
            'subtitle' => '',
            'id' => 1,
            'content' => '<p>Body</p>',
            'category_id' => 99,
            'published_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)
            ->resolveCategories([1 => 'Action', 2 => 'Health'])
            ->in($row);

        $this->assertEquals('99', $article->categories);
    }

    public function test_resolve_categories_maps_correctly(): void
    {
        $map = self::TEST_MAP;

        $row = [
            'title' => 'Title',
            'subtitle' => '',
            'id' => 1,
            'content' => '<p>Body</p>',
            'category_id' => 2,
            'published_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)
            ->resolveCategories([1 => 'Action', 2 => 'Health'])
            ->in($row);

        $this->assertEquals('Health', $article->categories);
    }

    public function test_published_at_returns_carbon_instance(): void
    {
        $map = self::TEST_MAP;

        $row = [
            'title' => 'Title',
            'subtitle' => '',
            'id' => 1,
            'content' => '<p>Body</p>',
            'category_id' => 1,
            'published_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)->in($row);

        $this->assertInstanceOf(Carbon::class, $article->publishedAt);
        $this->assertEquals('2024-01-15', $article->publishedAt->format('Y-m-d'));
    }

    public function test_set_add_hours(): void
    {
        $map = self::TEST_MAP;

        $row = [
            'title' => 'Title',
            'subtitle' => '',
            'id' => 1,
            'content' => '<p>Body</p>',
            'category_id' => 1,
            'published_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)
            ->setAddHours(5)
            ->in($row);

        $this->assertEquals('2024-01-15 15:00:00', $article->publishedAt->format('Y-m-d H:i:s'));
    }

    public function test_to_array_returns_all_fields(): void
    {
        $map = self::TEST_MAP;

        $row = [
            'title' => 'Title',
            'subtitle' => '',
            'id' => 1,
            'content' => '<p>Body content</p>',
            'category_id' => 1,
            'published_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)->in($row);

        $array = $article->toArray();

        $this->assertArrayHasKey('title', $array);
        $this->assertArrayHasKey('slug', $array);
        $this->assertArrayHasKey('categories', $array);
        $this->assertArrayHasKey('body', $array);
        $this->assertArrayHasKey('published_at', $array);
        $this->assertEquals('Body content', $array['body']);
    }

    public function test_body_converts_html_to_markdown(): void
    {
        $map = self::TEST_MAP;

        $row = [
            'title' => 'Title',
            'subtitle' => '',
            'id' => 1,
            'content' => '<h1>Heading</h1><p>Paragraph with <strong>bold</strong> text.</p>',
            'category_id' => 1,
            'published_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)->in($row);

        $this->assertStringContainsString('Heading', $article->markdown());
        $this->assertStringContainsString('Paragraph with', $article->markdown());
        $this->assertStringNotContainsString('<h1>', $article->markdown());
    }

    public function test_body_callback_chaining(): void
    {
        $map = self::TEST_MAP;

        $row = [
            'title' => 'Title',
            'subtitle' => '',
            'id' => 1,
            'content' => '<p>Hello World</p>',
            'category_id' => 1,
            'published_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)
            ->setBodyCallback(
                function ($item) {
                    return str_replace('Hello', 'Hi', $item);
                },
                function ($item) {
                    return str_replace('World', 'There', $item);
                }
            )
            ->in($row);

        $this->assertEquals('Hi There', $article->markdown());
    }

    public function test_make_returns_static_instance(): void
    {
        $map = self::TEST_MAP;

        $article = Article::make($map);

        $this->assertInstanceOf(Article::class, $article);
    }
}
