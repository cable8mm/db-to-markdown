<?php

namespace Cable8mm\DbToMarkdown\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'clean',
    description: 'Delete markdowns. run bin/console clean',
    hidden: false,
    aliases: ['trash', 'empty']
)]
class CleanCommand extends Command
{
    protected function configure()
    {
        $this->addOption(
            'path',
            'p',
            InputOption::VALUE_OPTIONAL,
            'Please specify the path to delete all files.',
            __DIR__.'/../../dist/'
        );
    }

    /**
     * Create Markdown files
     *
     * Run bin/console delete-markdowns
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        array_map('unlink', array_filter((array) glob($input->getOption('path').'*.markdown')));
        array_map('unlink', array_filter((array) glob($input->getOption('path').'*.md')));

        return Command::SUCCESS;
    }
}
