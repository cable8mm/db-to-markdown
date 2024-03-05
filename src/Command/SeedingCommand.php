<?php

namespace Cable8mm\DbToMarkdown\Command;

use Cable8mm\Database\Model;
use Cable8mm\Database\Seeder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'seeding',
    description: 'Seeding. run bin/console seeding',
    hidden: false,
    aliases: ['seed']
)]
class SeedingCommand extends Command
{
    /**
     * Seeding
     *
     * Run bin/console seeding
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        Model::getInstance()->down();

        Model::getInstance()->up();

        (new Seeder())->run();

        return Command::SUCCESS;
    }
}
