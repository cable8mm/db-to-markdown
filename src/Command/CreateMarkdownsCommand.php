<?php

namespace Cable8mm\DbToMarkdown\Command;

use Cable8mm\DbToMarkdown\Formats\IFormat;
use Cable8mm\DbToMarkdown\Formats\Markdown;
use Cable8mm\DbToMarkdown\Models\Article;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'create-markdowns',
    description: 'Creates markdowns. run bin/console create-markdowns',
    hidden: false,
    aliases: ['add-markdowns', 'create-md']
)]
class CreateMarkdownsCommand extends BaseCommand
{
    protected function makeFormat(Article $article): IFormat
    {
        return new Markdown(
            date: $article->publishedAt,
            body: $article->markdown(),
            slug: $article->slug
        );
    }
}
