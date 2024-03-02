<?php

namespace Cable8mm\DbToMarkdown\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'clean',
    description: 'Delete markdowns. run bin/console clean',
    hidden: false,
    aliases: ['trash', 'empty']
)]
class CleanCommand extends Command
{
    /**
     * Create Markdown files
     *
     * Run bin/console delete-markdowns
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        array_map('unlink', array_filter((array) glob(__DIR__.'/../../dist/*.markdown')));

        return Command::SUCCESS;
    }
}
