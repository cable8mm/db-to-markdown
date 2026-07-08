<?php

namespace Cable8mm\DbToMarkdown\Test;

use Cable8mm\DbToMarkdown\Command\CleanCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class CleanCommandTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir().'/db-to-markdown-test-'.uniqid();

        mkdir($this->tempDir, 0777, true);

        touch($this->tempDir.'/2024-01-15-post.md');
        touch($this->tempDir.'/2024-01-16-article.markdown');
        touch($this->tempDir.'/2024-01-17-story.markdown');
        touch($this->tempDir.'/readme.txt');
        touch($this->tempDir.'/.gitkeep');
    }

    protected function tearDown(): void
    {
        $files = array_merge(
            glob($this->tempDir.'/*'),
            glob($this->tempDir.'/.*')
        );

        array_map('unlink', array_filter($files, 'is_file'));
        rmdir($this->tempDir);
    }

    public function test_it_deletes_md_files(): void
    {
        $application = new Application;
        $application->add(new CleanCommand);

        $command = $application->find('clean');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            '--path' => $this->tempDir.'/',
        ]);

        $this->assertFileDoesNotExist($this->tempDir.'/2024-01-15-post.md');
        $this->assertFileDoesNotExist($this->tempDir.'/2024-01-16-article.markdown');
        $this->assertFileDoesNotExist($this->tempDir.'/2024-01-17-story.markdown');
    }

    public function test_it_keeps_non_markdown_files(): void
    {
        $application = new Application;
        $application->add(new CleanCommand);

        $command = $application->find('clean');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            '--path' => $this->tempDir.'/',
        ]);

        $this->assertFileExists($this->tempDir.'/readme.txt');
        $this->assertFileExists($this->tempDir.'/.gitkeep');
    }

    public function test_it_returns_success(): void
    {
        $application = new Application;
        $application->add(new CleanCommand);

        $command = $application->find('clean');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            '--path' => $this->tempDir.'/',
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }
}
