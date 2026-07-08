<?php

namespace Cable8mm\DbToMarkdown\Test;

use Cable8mm\DbToMarkdown\Command\CreateJekyllCommand;
use Cable8mm\DbToMarkdown\Command\CreateMarkdownsCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class BaseCommandTest extends TestCase
{
    public function test_create_md_command_has_correct_name(): void
    {
        $application = new Application();
        $application->add(new CreateMarkdownsCommand());

        $command = $application->find('create-markdowns');

        $this->assertEquals('create-markdowns', $command->getName());
    }

    public function test_create_md_command_has_aliases(): void
    {
        $application = new Application();
        $application->add(new CreateMarkdownsCommand());

        $command = $application->find('create-md');

        $this->assertNotNull($command);
        $this->assertEquals('create-markdowns', $command->getName());
    }

    public function test_create_jekyll_command_has_correct_name(): void
    {
        $application = new Application();
        $application->add(new CreateJekyllCommand());

        $command = $application->find('create-jekyll');

        $this->assertEquals('create-jekyll', $command->getName());
    }

    public function test_create_jekyll_command_has_aliases(): void
    {
        $application = new Application();
        $application->add(new CreateJekyllCommand());

        $command = $application->find('add-jekyll');

        $this->assertNotNull($command);
        $this->assertEquals('create-jekyll', $command->getName());
    }

    public function test_clean_command_has_aliases(): void
    {
        $application = new Application();
        $application->add(new \Cable8mm\DbToMarkdown\Command\CleanCommand());

        $command = $application->find('trash');

        $this->assertNotNull($command);
        $this->assertEquals('clean', $command->getName());
    }

    public function test_seeding_command_has_aliases(): void
    {
        $application = new Application();
        $application->add(new \Cable8mm\DbToMarkdown\Command\SeedingCommand());

        $command = $application->find('seed');

        $this->assertNotNull($command);
        $this->assertEquals('seeding', $command->getName());
    }
}
