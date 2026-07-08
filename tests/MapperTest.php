<?php

namespace Cable8mm\DbToMarkdown\Test;

use Cable8mm\DbToMarkdown\Mappers\Mapper;
use PHPUnit\Framework\TestCase;

final class MapperTest extends TestCase
{
    // Tests using inline arrays (no dependency on db-to-markdown.json)

    public function test_can_construct_with_string_title(): void
    {
        $mapper = new Mapper(
            title: 'single_title',
            slug: 'slug_field',
            categories: 'cat_field',
            body: 'body_field',
            published_at: 'date_field'
        );

        $fields = $mapper->fields();

        $this->assertEquals(['single_title', 'slug_field', 'cat_field', 'body_field', 'date_field'], $fields);
    }

    public function test_getters_return_expected_values(): void
    {
        $mapper = new Mapper(
            title: ['title1', 'title2'],
            slug: 'slug_field',
            categories: 'cat_field',
            body: 'body_field',
            published_at: 'date_field'
        );

        $this->assertEquals(['title1', 'title2'], $mapper->getTitle());
        $this->assertEquals('slug_field', $mapper->getSlug());
        $this->assertEquals('cat_field', $mapper->getCategories());
        $this->assertEquals('body_field', $mapper->getBody());
        $this->assertEquals('date_field', $mapper->getPublishedAt());
    }

    public function test_is_title_array_returns_true_for_array(): void
    {
        $mapper = new Mapper(
            title: ['title1', 'title2'],
            slug: 'slug',
            categories: 'cat',
            body: 'body',
            published_at: 'date'
        );

        $this->assertTrue($mapper->isTitleArray());
    }

    public function test_is_title_array_returns_false_for_string(): void
    {
        $mapper = new Mapper(
            title: 'single_title',
            slug: 'slug',
            categories: 'cat',
            body: 'body',
            published_at: 'date'
        );

        $this->assertFalse($mapper->isTitleArray());
    }

    public function test_to_array_returns_all_fields(): void
    {
        $mapper = new Mapper(
            title: 'single_title',
            slug: 'slug_field',
            categories: 'cat_field',
            body: 'body_field',
            published_at: 'date_field'
        );

        $array = $mapper->toArray();

        $this->assertEquals([
            'title' => 'single_title',
            'slug' => 'slug_field',
            'categories' => 'cat_field',
            'body' => 'body_field',
            'published_at' => 'date_field',
        ], $array);
    }

    public function test_get_map_throws_for_missing_file(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('bin/console init');

        Mapper::getMap('/nonexistent/path/file.json');
    }

    public function test_get_map_throws_for_invalid_json(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'mapper_test').'.json';
        file_put_contents($tempFile, 'not valid json {');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('bin/console init');

        try {
            Mapper::getMap($tempFile);
        } finally {
            unlink($tempFile);
        }
    }

    public function test_get_map_throws_for_missing_required_fields(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'mapper_test').'.json';
        file_put_contents($tempFile, json_encode(['map' => ['title' => 't']]));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('missing required map fields');

        try {
            Mapper::getMap($tempFile);
        } finally {
            unlink($tempFile);
        }
    }
}
