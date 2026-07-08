<?php

namespace Cable8mm\DbToMarkdown\Test;

use Cable8mm\DbToMarkdown\Command\InitCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class InitCommandTest extends TestCase
{
    private string $workDir;

    private string $originalDir;

    protected function setUp(): void
    {
        $this->originalDir = getcwd();

        $this->workDir = sys_get_temp_dir().'/db-to-markdown-init-'.uniqid();

        mkdir($this->workDir);

        copy($this->originalDir.'/.env.example', $this->workDir.'/.env.example');
        copy($this->originalDir.'/db-to-markdown.json.example', $this->workDir.'/db-to-markdown.json.example');

        chdir($this->workDir);
    }

    protected function tearDown(): void
    {
        chdir($this->originalDir);

        foreach (['.env', '.env.example', 'db-to-markdown.json', 'db-to-markdown.json.example'] as $file) {
            $path = $this->workDir.'/'.$file;

            if (file_exists($path)) {
                unlink($path);
            }
        }

        rmdir($this->workDir);
    }

    public function test_init_command_has_correct_name(): void
    {
        $application = new Application;
        $application->add(new InitCommand);

        $command = $application->find('init');

        $this->assertEquals('init', $command->getName());
    }

    public function test_it_creates_files_from_examples(): void
    {
        $application = new Application;
        $application->add(new InitCommand);

        $tester = new CommandTester($application->find('init'));
        $tester->execute([], ['cwd' => $this->workDir]);

        $this->assertFileExists($this->workDir.'/.env');
        $this->assertFileExists($this->workDir.'/db-to-markdown.json');
        $this->assertStringContainsString('Created', $tester->getDisplay());
    }

    public function test_it_skips_existing_files(): void
    {
        file_put_contents($this->workDir.'/.env', 'EXISTING');
        file_put_contents($this->workDir.'/db-to-markdown.json', '{"existing":true}');

        $application = new Application;
        $application->add(new InitCommand);

        $tester = new CommandTester($application->find('init'));
        $tester->execute([], ['cwd' => $this->workDir]);

        $this->assertStringContainsString('Skipped', $tester->getDisplay());
        $this->assertStringEqualsFile($this->workDir.'/.env', 'EXISTING');
        $this->assertStringEqualsFile($this->workDir.'/db-to-markdown.json', '{"existing":true}');
    }
}
