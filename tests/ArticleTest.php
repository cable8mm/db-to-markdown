<?php

namespace Cable8mm\DbToMarkdown\Test;

use Cable8mm\DbToMarkdown\Mappers\Mapper;
use Cable8mm\DbToMarkdown\Models\Article;
use Carbon\Carbon;
use Faker\Factory;
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
        $html = '<p>[widget type=&quot;good&quot; good=&quot;1000014584&quot;]</p>'.PHP_EOL;
        $html .= '<p>only this to remain</p>';

        $row = [
            'cover_title' => $faker->word(),
            'front_title1' => $faker->word(),
            'front_title2' => $faker->word(),
            'id' => 1,
            'contents' => $html,
            'cast_category_id' => $faker->numberBetween(0, 7),
            'display_at' => new Carbon(),
        ];

        $article = Article::make($map)
            ->setBodyCallback(
                function ($item) {
                    return preg_replace('/<img[^>]+>/', '', $item);
                }, function ($item) {
                    // \[widget type="good" good="1000012875"\]
                    return preg_replace('/\[[^\]]+\]\n?/u', '', $item);
                }
            )
            ->in($row);

        $this->assertEquals('only this to remain', $article->markdown());
    }

    public function test_regular_expression()
    {
        $in = '\[widget type="good" good="1000012875"\]'.PHP_EOL;
        $in .= '\[widget type="good" good="1000013673"\]'.PHP_EOL;
        $in .= '\[widget type="good" good="1000012875"\]'.PHP_EOL;
        $in .= '\[widget type="good" good="1000012875"\]';

        $actual = preg_replace('/\\\\\[[^\]]+\]\n?/', '', $in);

        $this->assertEmpty($actual);
    }
}
