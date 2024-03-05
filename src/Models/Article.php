<?php

namespace Cable8mm\DbToMarkdown\Models;

use Carbon\Carbon;
use League\HTMLToMarkdown\HtmlConverter;

class Article
{
    // below properties follow by https://import.jekyllrb.com/docs/csv/
    public string $title;

    public string $slug;

    public string $body;

    private $bodyCallbacks;

    public Carbon $publishedAt;

    private ?int $addHours = null;

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

    private function slug(): void
    {
        $this->slug = preg_replace('/[_ ]/', '-', $this->row[$this->map['slug']]);
    }

    private function body(): void
    {
        $converter = new HtmlConverter();

        $body = $this->row[$this->map['body']];

        if (! is_null($this->bodyCallbacks)) {
            foreach ($this->bodyCallbacks as $callback) {
                $body = ($callback)($body);
            }
        }

        $this->body = strip_tags($converter->convert($body));
    }

    private function publishedAt(): Carbon
    {
        if (! empty($this->publishedAt)) {
            return $this->publishedAt;
        }

        $publishedAt = new Carbon($this->row[$this->map['published_at']]);

        if (! is_null($this->addHours)) {
            $publishedAt = $publishedAt->addHours($this->addHours);
        }

        return $this->publishedAt = $publishedAt;
    }

    public function markdown(): string
    {
        return $this->body;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'body' => $this->body,
            'published_at' => $this->publishedAt,
        ];
    }

    public function in(array $row): static
    {
        $this->row = $row;

        $this->title();
        $this->slug();
        $this->body();
        $this->publishedAt();

        return $this;
    }

    public function setBodyCallback(...$callbacks): static
    {
        $this->bodyCallbacks = $callbacks;

        return $this;
    }

    public function setAddHours(int $hours): static
    {
        $this->addHours = $hours;

        return $this;
    }

    public static function make($map): static
    {
        $article = new static();

        $article->map = $map;

        return $article;
    }
}
