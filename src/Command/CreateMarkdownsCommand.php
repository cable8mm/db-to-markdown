<?php

namespace Cable8mm\ImportFromDbToJekyll\Command;

use Cable8mm\ImportFromDbToJekyll\DB;
use Dotenv\Dotenv;
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln($this->database->info()['driver']);

        return Command::SUCCESS;
    }
}
