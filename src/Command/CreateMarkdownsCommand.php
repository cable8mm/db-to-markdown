<?php

namespace Cable8mm\ImportFromDbToJekyll\Command;

use Cable8mm\ImportFromDbToJekyll\DB;
use Cable8mm\ImportFromDbToJekyll\Mappers\Mapper;
use Cable8mm\ImportFromDbToJekyll\Models\Article;
use Medoo\Medoo;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'create-markdowns',
    description: 'Creates markdowns.',
    hidden: false,
    aliases: ['add-markdowns']
)]
class CreateMarkdownsCommand extends Command
{
    private Medoo $database;

    protected function configure()
    {
        $this->database = DB::getInstance()->getConnection();
    }

    /**
     * Create Markdown files
     *
     * Run ./bin/console create-markdowns
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $map = Mapper::getMap('DogStory');

        $mapper = new Mapper(...$map);

        $articles = $this->database->select(DB::table(), $mapper->fields(), $mapper->where());

        foreach ($articles as $row) {
            $article = Article::make($map)->in($row);

            file_put_contents(__DIR__.'/../../dist/'.$article->permalink, $article->markdown());
        }

        return Command::SUCCESS;
    }
}
