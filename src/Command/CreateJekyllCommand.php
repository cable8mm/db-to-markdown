<?php

namespace Cable8mm\DbToMarkdown\Command;

use Cable8mm\DbToMarkdown\DB;
use Cable8mm\DbToMarkdown\Formats\Jekyll;
use Cable8mm\DbToMarkdown\Mappers\Mapper;
use Cable8mm\DbToMarkdown\Models\Article;
use Medoo\Medoo;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'create-jekyll',
    description: 'Creates markdowns. run bin/console create-jekyll',
    hidden: false,
    aliases: ['add-jekyll']
)]
class CreateJekyllCommand extends Command
{
    private Medoo $database;

    protected function configure()
    {
        $this->database = DB::getInstance()->getConnection();
    }

    /**
     * Create Markdown files for Jekyll
     *
     * Run bin/console create-jekyll
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $map = Mapper::getMap('DogStory');

        $mapper = new Mapper(...$map);

        $articles = $this->database->select(DB::table(), $mapper->fields(), $mapper->where());

        foreach ($articles as $row) {
            $article = Article::make($map)
                ->setBodyCallback(
                    function ($item) {
                        return preg_replace('/<img[^>]+>/', '', $item);
                    }, function ($item) {
                        return preg_replace('/\\\\\[[^\]]+\]\n?/', '', $item);
                    }
                )
                ->setAddHours(24 * 365 + 24 * 120)
                ->in($row);

            $jekyll = new Jekyll(
                layout: 'single',
                title: $article->title,
                date: $article->publishedAt,
                author: 'Samgu Lee',
                body: $article->markdown(),
                slug: $article->slug
            );

            file_put_contents(__DIR__.'/../../dist/'.$jekyll->path(), $jekyll->render());
        }

        return Command::SUCCESS;
    }
}
