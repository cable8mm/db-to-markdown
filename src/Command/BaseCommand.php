<?php

namespace Cable8mm\DbToMarkdown\Command;

use Cable8mm\DbToMarkdown\DB;
use Cable8mm\DbToMarkdown\Formats\IFormat;
use Cable8mm\DbToMarkdown\Mappers\Mapper;
use Cable8mm\DbToMarkdown\Models\Article;
use Medoo\Medoo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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

    protected function configure(): void
    {
        $this->addOption(
            'dry-run',
            null,
            InputOption::VALUE_NONE,
            'Simulate the process without writing files'
        );

        $this->addOption(
            'limit',
            'l',
            InputOption::VALUE_OPTIONAL,
            'Limit the number of records to process',
            null
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $map = Mapper::getMap(DB::mapper());
        } catch (\Throwable $e) {
            $output->writeln(sprintf(
                '<error>Mapper "%s" not found. Please check DB_MAPPER in .env</error>',
                DB::mapper()
            ));

            return Command::FAILURE;
        }

        $mapper = new Mapper(...$map);

        $where = $mapper->where();

        $limit = $input->getOption('limit');

        if ($limit !== null) {
            $where['LIMIT'] = (int) $limit;
        }

        $articles = $this->database->select(DB::table(), $mapper->fields(), $where);

        $outputDir = rtrim($this->getOutputDir($input), '/').'/';

        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $count = 0;

        $isDryRun = $input->getOption('dry-run');

        if ($isDryRun) {
            $output->writeln(sprintf(
                '<comment>DRY RUN: Would generate %d files in %s</comment>',
                count($articles),
                $outputDir
            ));

            return Command::SUCCESS;
        }

        foreach ($articles as $row) {
            $article = $this->configureArticle(Article::make($map)->in($row));

            $format = $this->makeFormat($article);

            file_put_contents($outputDir.$format->path(), $format->render());

            $count++;
        }

        $output->writeln(sprintf('<info>Generated %d files in %s</info>', $count, $outputDir));

        return Command::SUCCESS;
    }
}
