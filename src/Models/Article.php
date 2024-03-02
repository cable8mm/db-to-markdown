<?php

namespace Cable8mm\ImportFromDbToJekyll\Models;

use Carbon\Carbon;
use InvalidArgumentException;

class Article
{
    // below properties follow by https://import.jekyllrb.com/docs/csv/

    protected string $title;

    protected string $permalink;

    protected string $body;

    protected Carbon $published_at;

    protected string $filter;

    private const VALID_FILTERS = ['markdown', 'textile'];

    // Setters

    public function title(string $title): static
    {
        $this->title = $title;

        return new static();
    }

    public function permalink(string $permalink): static
    {
        $this->permalink = $permalink;

        return new static();
    }

    public function body(string $body): static
    {
        $this->body = $body;

        return new static();
    }

    public function publishedAt(string $publishedAt): static
    {
        $this->published_at = new Carbon($publishedAt);

        return new static();
    }

    public function filter(string $filter): static
    {
        if (in_array($filter, self::VALID_FILTERS)) {
            throw new InvalidArgumentException('This filter isn\'t invalid. Please to change filter\'s name.');
        }

        $this->filter = $filter;

        return new static();
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'permalink' => $this->permalink,
            'body' => $this->body,
            'published_at' => $this->published_at,
            'filter' => $this->filter,
        ];
    }
}
