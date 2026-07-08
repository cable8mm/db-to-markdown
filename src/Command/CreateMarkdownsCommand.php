<?php

namespace Cable8mm\DbToMarkdown\Command;

use Cable8mm\DbToMarkdown\Formats\IFormat;
use Cable8mm\DbToMarkdown\Formats\Markdown;
use Cable8mm\DbToMarkdown\Models\Article;
use Symfony\Component\Console\Attribute\AsCommand;

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
