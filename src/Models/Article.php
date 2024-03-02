<?php

namespace Cable8mm\DbToMarkdown\Models;

use Carbon\Carbon;
use League\HTMLToMarkdown\HtmlConverter;

class Article
{
    // below properties follow by https://import.jekyllrb.com/docs/csv/
    public string $title;

    public string $permalink;

    public string $body;

    public Carbon $publishedAt;

    private array $map;

    /**
     * A row from database.
     */
    private array $row;

    const EXTENSION = 'markdown';

    public function __construct()
    {
    }

    private function title(): void
    {
        if (is_string($this->map['title'])) {
            $this->title = $this->row[$this->map['title']];

            return;
        }

        $title = '';

        foreach ($this->map['title'] as $item) {
            $title .= $this->row[$item].' ';
        }

        $this->title = preg_replace('/\s+$/', '', $title);
    }

    private function permalink(): void
    {
        $prefix = $this->publishedAt()->format('Y-m-d');

        $this->permalink = $prefix.'-'.preg_replace('/[_ ]/', '-', $this->row[$this->map['permalink']]).'.'.self::EXTENSION;
    }

    private function body(): void
    {
        $converter = new HtmlConverter();

        $this->body = strip_tags($converter->convert($this->row[$this->map['body']]));
    }

    private function publishedAt(): Carbon
    {
        if (! empty($this->publishedAt)) {
            return $this->publishedAt;
        }

        return $this->publishedAt = new Carbon($this->row[$this->map['published_at']]);
    }

    public function markdown(): string
    {
        return $this->body;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'permalink' => $this->permalink,
            'body' => $this->body,
            'published_at' => $this->publishedAt,
        ];
    }

    public function in(array $row): static
    {
        $this->row = $row;

        $this->title();
        $this->permalink();
        $this->body();
        $this->publishedAt();

        return $this;
    }

    public static function make($map): static
    {
        $article = new static();

        $article->map = $map;

        return $article;
    }
}
