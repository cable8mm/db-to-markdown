<?php

namespace Cable8mm\DbToMarkdown\Test;

use Cable8mm\DbToMarkdown\Formats\Markdown;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

final class MarkdownTest extends TestCase
{
    public function test_it_can_render_body(): void
    {
        $markdown = new Markdown(
            date: new Carbon('2024-01-15'),
            body: '# Hello World'.PHP_EOL.PHP_EOL.'This is content.',
            slug: 'hello-world'
        );

        $render = $markdown->render();

        $this->assertEquals('# Hello World'.PHP_EOL.PHP_EOL.'This is content.', $render);
    }

    public function test_it_can_generate_path_with_md_extension(): void
    {
        $now = new Carbon('2024-01-15');

        $markdown = new Markdown(
            date: $now,
            body: 'Content',
            slug: 'hello-world'
        );

        $path = $markdown->path();

        $this->assertEquals('2024-01-15-hello-world.md', $path);
    }

    public function test_it_can_generate_path_with_custom_extension(): void
    {
        $now = new Carbon('2024-01-15');

        $markdown = new Markdown(
            date: $now,
            body: 'Content',
            slug: 'hello-world'
        );

        $path = $markdown->path('markdown');

        $this->assertEquals('2024-01-15-hello-world.markdown', $path);
    }

    public function test_it_replaces_underscores_and_spaces_in_slug(): void
    {
        $now = new Carbon('2024-01-15');

        $markdown = new Markdown(
            date: $now,
            body: 'Content',
            slug: 'hello_world test_slug'
        );

        $path = $markdown->path();

        $this->assertEquals('2024-01-15-hello-world-test-slug.md', $path);
    }

    public function test_it_returns_default_extension(): void
    {
        $markdown = new Markdown(
            date: new Carbon('2024-01-15'),
            body: 'Content',
            slug: 'slug'
        );

        $this->assertEquals('md', $markdown->getExtension());
    }
}
