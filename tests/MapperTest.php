<?php

namespace Cable8mm\DbToMarkdown\Test;

use Cable8mm\DbToMarkdown\Mappers\Mapper;
use PHPUnit\Framework\TestCase;

final class MapperTest extends TestCase
{
    public function test_can_get_map_what_you_want(): void
    {
        $map = Mapper::getMap('DogStory');

        $this->assertIsArray($map);
        $this->assertArrayHasKey('title', $map);
        $this->assertArrayHasKey('slug', $map);
        $this->assertArrayHasKey('categories', $map);
        $this->assertArrayHasKey('body', $map);
        $this->assertArrayHasKey('published_at', $map);
    }

    public function test_can_get_flatten_fields_from_mapper(): void
    {
        $map = Mapper::getMap('DogStory');

        $fields = (new Mapper(...$map))->fields();

        $this->assertIsArray($fields);
        $this->assertContains('cover_title', $fields);
        $this->assertContains('front_title1', $fields);
        $this->assertContains('front_title2', $fields);
        $this->assertContains('id', $fields);
        $this->assertContains('cast_category_id', $fields);
        $this->assertContains('contents', $fields);
        $this->assertContains('display_at', $fields);
    }

    public function test_can_get_where_condition(): void
    {
        $map = Mapper::getMap('DogStory');

        $where = (new Mapper(...$map))->where();

        $this->assertIsArray($where);
        $this->assertArrayHasKey('cover_title[!]', $where);
        $this->assertArrayHasKey('front_title1[!]', $where);
        $this->assertArrayHasKey('front_title2[!]', $where);
        $this->assertArrayHasKey('id[!]', $where);
        $this->assertArrayHasKey('cast_category_id[!]', $where);
        $this->assertArrayHasKey('contents[!]', $where);
        $this->assertArrayHasKey('display_at[!]', $where);

        foreach ($where as $value) {
            $this->assertNull($value);
        }
    }

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
}
