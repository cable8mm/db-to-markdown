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

    public static function getMap($filename)
    {
        return require __DIR__.DIRECTORY_SEPARATOR.$filename.'.php';
    }
}
