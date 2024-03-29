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
use Symfony\Component\Console\Input\InputOption;
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

    private array $categories = [
        0 => '기타',
        '행동+심리',
        '의료/건강',
        '감동',
        '입양',
        '재미',
        '장소',
        '상품',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->database = DB::getInstance()->getConnection();
    }

    protected function configure()
    {
        $this->addOption(
            'output',
            'o',
            InputOption::VALUE_OPTIONAL,
            'To which destination do the files output?',
            __DIR__.'/../../dist/'
        );
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
                        return preg_replace('/<img[^>]+>/u', '', $item);
                    }, function ($item) {
                        return preg_replace('/\[[^\]]+\]\n?/u', '', $item);
                    }
                )
                ->resolveCategories($this->categories)
                ->in($row);

            $jekyll = new Jekyll(
                layout: 'single',
                title: $article->title,
                date: $article->publishedAt,
                author: 'Companimal',
                body: $article->markdown(),
                slug: $article->slug,
                categories: $article->categories,
            );

            file_put_contents($input->getOption('output').$jekyll->path(), $jekyll->render());
        }

        return Command::SUCCESS;
    }
}
