<?php

namespace Cable8mm\DbToMarkdown\Mappers;

use function Cable8mm\ArrayFlatten\array_flatten;

class Mapper
{
    private string|array $title;

    private string $slug;

    private string $categories;

    private string $body;

    private string|array $published_at;

    public function __construct(string|array $title, string $slug, string $categories, string $body, string|array $published_at)
    {
        $this->title = $title;
        $this->slug = $slug;
        $this->categories = $categories;
        $this->body = $body;
        $this->published_at = $published_at;
    }

    public function fields(): array
    {
        $properties = [
            $this->title,
            $this->slug,
            $this->categories,
            $this->body,
            $this->published_at,
        ];

        return array_flatten($properties);
    }

    public function where(): array
    {
        $flatten = $this->fields();

        $where = [];

        foreach ($flatten as $item) {
            $where[$item.'[!]'] = null;
        }

        return $where;
    }

    public static function getMap(string $path): array
    {
        if (! is_file($path) || pathinfo($path, PATHINFO_EXTENSION) !== 'json') {
            throw new \InvalidArgumentException($path.' is not a valid JSON mapper file.');
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        if (! is_array($decoded)) {
            throw new \InvalidArgumentException($path.' is not a valid JSON mapper file.');
        }

        return $decoded['map'] ?? $decoded;
    }

    public function getTitle(): string|array
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getCategories(): string
    {
        return $this->categories;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getPublishedAt(): string|array
    {
        return $this->published_at;
    }

    public function isTitleArray(): bool
    {
        return is_array($this->title);
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'categories' => $this->categories,
            'body' => $this->body,
            'published_at' => $this->published_at,
        ];
    }
}
