<?php

namespace Cable8mm\DbToMarkdown\Test;

use Cable8mm\DbToMarkdown\Mappers\Mapper;
use Cable8mm\DbToMarkdown\Models\Article;
use Carbon\Carbon;
use Faker\Factory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ArticleTest extends TestCase
{
    public function test_callbacks_to_work_collectly(): void
    {
        $map = Mapper::getMap('DogStory');

        $faker = Factory::create();

        $html = '<p>[widget id="good" ]</p>'.PHP_EOL;
        $html = '<p>[widget type="good" good="1000014584"]</p>'.PHP_EOL;
        $html .= '<p><img src="https://www.palgle.com/image.png" /></p>'.PHP_EOL;
        $html = '<p>[widget type="good" good="1000014584"]</p>'.PHP_EOL;
        $html .= '<p>only this to remain</p>';

        $row = [
            'cover_title' => $faker->word(),
            'front_title1' => $faker->word(),
            'front_title2' => $faker->word(),
            'id' => 1,
            'contents' => $html,
            'cast_category_id' => $faker->numberBetween(0, 7),
            'display_at' => new Carbon,
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
        $map = Mapper::getMap('DogStory');

        $row = [
            'cover_title' => 'Single Title',
            'front_title1' => '',
            'front_title2' => '',
            'id' => 1,
            'contents' => 'Body content',
            'cast_category_id' => 1,
            'display_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)->in($row);

        $this->assertEquals('Single Title', $article->title);
    }

    public function test_title_concatenates_when_map_title_is_array(): void
    {
        $map = [
            'title' => ['front_title1', 'front_title2'],
            'slug' => 'id',
            'categories' => 'cast_category_id',
            'body' => 'contents',
            'published_at' => 'display_at',
        ];

        $row = [
            'front_title1' => 'Hello',
            'front_title2' => 'World',
            'id' => 1,
            'contents' => 'Body content',
            'cast_category_id' => 1,
            'display_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)->in($row);

        $this->assertEquals('Hello World', $article->title);
    }

    public function test_slug_replaces_underscores_and_spaces(): void
    {
        $map = Mapper::getMap('DogStory');

        $row = [
            'cover_title' => 'Title',
            'front_title1' => '',
            'front_title2' => '',
            'id' => 100,
            'contents' => 'Body',
            'cast_category_id' => 1,
            'display_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)->in($row);

        $this->assertStringContainsString('100', $article->slug);
    }

    public function test_categories_is_nullable(): void
    {
        $map = [
            'title' => 'cover_title',
            'slug' => 'id',
            'categories' => 'cast_category_id',
            'body' => 'contents',
            'published_at' => 'display_at',
        ];

        $row = [
            'cover_title' => 'Title',
            'id' => 1,
            'contents' => 'Body',
            'cast_category_id' => null,
            'display_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)->in($row);

        $this->assertNull($article->categories);
    }

    public function test_resolve_categories_throws_exception_for_null(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $map = Mapper::getMap('DogStory');

        $row = [
            'cover_title' => 'Title',
            'front_title1' => '',
            'front_title2' => '',
            'id' => 1,
            'contents' => 'Body',
            'cast_category_id' => null,
            'display_at' => '2024-01-15 10:00:00',
        ];

        Article::make($map)
            ->resolveCategories([0 => '기타', 1 => '행동'])
            ->in($row);
    }

    public function test_resolve_categories_throws_exception_for_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $map = Mapper::getMap('DogStory');

        $row = [
            'cover_title' => 'Title',
            'front_title1' => '',
            'front_title2' => '',
            'id' => 1,
            'contents' => 'Body',
            'cast_category_id' => 99,
            'display_at' => '2024-01-15 10:00:00',
        ];

        Article::make($map)
            ->resolveCategories([0 => '기타', 1 => '행동'])
            ->in($row);
    }

    public function test_resolve_categories_maps_correctly(): void
    {
        $map = Mapper::getMap('DogStory');

        $row = [
            'cover_title' => 'Title',
            'front_title1' => '',
            'front_title2' => '',
            'id' => 1,
            'contents' => 'Body',
            'cast_category_id' => 2,
            'display_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)
            ->resolveCategories([0 => '기타', 1 => '행동+심리', 2 => '의료/건강'])
            ->in($row);

        $this->assertEquals('의료/건강', $article->categories);
    }

    public function test_published_at_returns_carbon_instance(): void
    {
        $map = Mapper::getMap('DogStory');

        $row = [
            'cover_title' => 'Title',
            'front_title1' => '',
            'front_title2' => '',
            'id' => 1,
            'contents' => 'Body',
            'cast_category_id' => 1,
            'display_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)->in($row);

        $this->assertInstanceOf(Carbon::class, $article->publishedAt);
        $this->assertEquals('2024-01-15', $article->publishedAt->format('Y-m-d'));
    }

    public function test_set_add_hours(): void
    {
        $map = Mapper::getMap('DogStory');

        $row = [
            'cover_title' => 'Title',
            'front_title1' => '',
            'front_title2' => '',
            'id' => 1,
            'contents' => 'Body',
            'cast_category_id' => 1,
            'display_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)
            ->setAddHours(5)
            ->in($row);

        $this->assertEquals('2024-01-15 15:00:00', $article->publishedAt->format('Y-m-d H:i:s'));
    }

    public function test_to_array_returns_all_fields(): void
    {
        $map = Mapper::getMap('DogStory');

        $row = [
            'cover_title' => 'Title',
            'front_title1' => '',
            'front_title2' => '',
            'id' => 1,
            'contents' => 'Body content',
            'cast_category_id' => 1,
            'display_at' => '2024-01-15 10:00:00',
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
        $map = Mapper::getMap('DogStory');

        $row = [
            'cover_title' => 'Title',
            'front_title1' => '',
            'front_title2' => '',
            'id' => 1,
            'contents' => '<h1>Heading</h1><p>Paragraph with <strong>bold</strong> text.</p>',
            'cast_category_id' => 1,
            'display_at' => '2024-01-15 10:00:00',
        ];

        $article = Article::make($map)->in($row);

        $this->assertStringContainsString('Heading', $article->markdown());
        $this->assertStringContainsString('Paragraph with', $article->markdown());
        $this->assertStringNotContainsString('<h1>', $article->markdown());
    }

    public function test_body_callback_chaining(): void
    {
        $map = Mapper::getMap('DogStory');

        $row = [
            'cover_title' => 'Title',
            'front_title1' => '',
            'front_title2' => '',
            'id' => 1,
            'contents' => '<p>Hello World</p>',
            'cast_category_id' => 1,
            'display_at' => '2024-01-15 10:00:00',
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
        $map = Mapper::getMap('DogStory');

        $article = Article::make($map);

        $this->assertInstanceOf(Article::class, $article);
    }
}
