<?php

namespace Cable8mm\ImportFromDbToJekyll\Command;

use Cable8mm\ImportFromDbToJekyll\Models\Article;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'delete-markdowns',
    description: 'Delete markdowns. run bin/console delete-markdowns',
    hidden: false,
    aliases: ['remove-markdowns']
)]
class DeleteMarkdownsCommand extends Command
{
    /**
     * Create Markdown files
     *
     * Run bin/console delete-markdowns
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        array_map('unlink', array_filter((array) glob(__DIR__.'/../../dist/*.'.Article::EXTENSION)));

        return Command::SUCCESS;
    }
}
