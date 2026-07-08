<?php

namespace Cable8mm\DbToMarkdown\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'init',
    description: 'Initialize .env and db-to-markdown.json from their examples.',
    hidden: false,
    aliases: []
)]
class InitCommand extends Command
{
    /**
     * @var array<string, string> Map of source example file to target file.
     */
    private const FILES = [
        '.env.example' => '.env',
        'db-to-markdown.json.example' => 'db-to-markdown.json',
    ];

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach (self::FILES as $source => $target) {
            if (file_exists($target)) {
                $output->writeln(sprintf('<comment>Skipped %s (already exists)</comment>', $target));

                continue;
            }

            if (! file_exists($source)) {
                $output->writeln(sprintf('<error>Missing %s, cannot create %s</error>', $source, $target));

                return Command::FAILURE;
            }

            copy($source, $target);

            $output->writeln(sprintf('<info>Created %s from %s</info>', $target, $source));
        }

        return Command::SUCCESS;
    }
}
