<?php

namespace Cable8mm\DbToMarkdown\Command;

use Cable8mm\DbToMarkdown\DB;
use Cable8mm\DbToMarkdown\Formats\IFormat;
use Cable8mm\DbToMarkdown\Mappers\Mapper;
use Cable8mm\DbToMarkdown\Models\Article;
use Medoo\Medoo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends Command
{
    protected Medoo $database;

    public function __construct()
    {
        parent::__construct();

        $this->database = DB::getInstance()->getConnection();
    }

    /**
     * Create the format object from an Article.
     */
    abstract protected function makeFormat(Article $article): IFormat;

    /**
     * Apply any additional configuration to an Article before processing.
     */
    protected function configureArticle(Article $article): Article
    {
        return $article;
    }

    /**
     * Get the output directory for generated files.
     */
    protected function getOutputDir(InputInterface $input): string
    {
        return __DIR__.'/../../dist/';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $map = Mapper::getMap(DB::mapper());

        $mapper = new Mapper(...$map);

        $articles = $this->database->select(DB::table(), $mapper->fields(), $mapper->where());

        $outputDir = rtrim($this->getOutputDir($input), '/').'/';

        foreach ($articles as $row) {
            $article = $this->configureArticle(Article::make($map)->in($row));

            $format = $this->makeFormat($article);

            file_put_contents($outputDir.$format->path(), $format->render());
        }

        $output->writeln(sprintf('<info>Generated %d files in %s</info>', count($articles), $outputDir));

        return Command::SUCCESS;
    }
}
