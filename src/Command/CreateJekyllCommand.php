<?php

namespace Cable8mm\DbToMarkdown\Command;

use Cable8mm\DbToMarkdown\DB;
use Cable8mm\DbToMarkdown\Formats\IFormat;
use Cable8mm\DbToMarkdown\Formats\Jekyll;
use Cable8mm\DbToMarkdown\Models\Article;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(
    name: 'create-jekyll',
    description: 'Creates markdowns. run bin/console create-jekyll',
    hidden: false,
    aliases: ['add-jekyll']
)]
class CreateJekyllCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this->addOption(
            'output',
            'o',
            InputOption::VALUE_OPTIONAL,
            'To which destination do the files output?',
            __DIR__.'/../../dist/'
        );
    }

    protected function getOutputDir(InputInterface $input): string
    {
        return $input->getOption('output');
    }

    protected function configureArticle(Article $article): Article
    {
        $categories = DB::categories();

        return $article
            ->setBodyCallback(
                function ($item) {
                    return preg_replace('/<img[^>]+>/u', '', $item);
                },
                function ($item) {
                    return preg_replace('/\[[^\]]+\]\n?/u', '', $item);
                }
            )
            ->resolveCategories($categories);
    }

    protected function makeFormat(Article $article): IFormat
    {
        return new Jekyll(
            layout: 'single',
            title: $article->title,
            date: $article->publishedAt,
            author: DB::author(),
            body: $article->markdown(),
            slug: $article->slug,
            categories: $article->categories,
        );
    }
}
