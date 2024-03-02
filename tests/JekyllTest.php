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
            date: new Carbon(),
            author: 'Samgu Lee',
            body: '## Heading'.PHP_EOL.PHP_EOL.'Contents',
            slug: '1'
        );

        $render = $jekyll->render();

        $this->assertNotNull($render);
    }

    public function test_it_must_use_camelcase_filename(): void
    {
        $now = new Carbon();

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
}
