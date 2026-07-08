<?php

namespace Cable8mm\DbToMarkdown\Models;

use Cable8mm\DbToMarkdown\Mappers\Mapper;
use Carbon\Carbon;
use InvalidArgumentException;
use League\HTMLToMarkdown\HtmlConverter;

class Article
{
    // below properties follow by https://import.jekyllrb.com/docs/csv/
    public string $title;

    public string $slug;

    public ?string $categories = null;

    private $resolveCategories;

    public string $body;

    private $bodyCallbacks;

    public Carbon $publishedAt;

    private ?int $addHours = null;

    private Mapper $mapper;

    private HtmlConverter $converter;

    /**
     * A row from database.
     */
    private array $row;

    const EXTENSION = 'markdown';

    public function __construct()
    {
        $this->converter = new HtmlConverter();
    }

    private function title(): void
    {
        $titleField = $this->mapper->getTitle();

        if (is_string($titleField)) {
            $this->title = $this->row[$titleField];

            return;
        }

        $title = '';

        foreach ($titleField as $item) {
            $title .= $this->row[$item].' ';
        }

        $title = preg_replace('/\s+$/', '', $title);

        $this->title = preg_replace('/"/', '\"', $title);
    }

    private function slug(): void
    {
        $this->slug = preg_replace('/[_ ]/', '-', $this->row[$this->mapper->getSlug()]);
    }

    private function categories(): void
    {
        $this->categories = $this->row[$this->mapper->getCategories()];

        if (! is_null($this->resolveCategories)) {
            if (is_null($this->categories)) {
                throw new InvalidArgumentException($this->slug.' slug\'s category must exist.');
            }

            if (! in_array($this->categories, array_keys($this->resolveCategories))) {
                throw new InvalidArgumentException($this->categories.' is an invalid category. Categories must include one of '.implode(',', array_keys($this->resolveCategories)));
            }

            $this->categories = $this->resolveCategories[$this->categories];
        }
    }

    private function body(): void
    {
        $body = $this->row[$this->mapper->getBody()];

        if (! is_null($this->bodyCallbacks)) {
            foreach ($this->bodyCallbacks as $callback) {
                $body = ($callback)($body);
            }
        }

        $this->body = strip_tags($this->converter->convert($body));
    }

    private function publishedAt(): Carbon
    {
        if (! empty($this->publishedAt)) {
            return $this->publishedAt;
        }

        $publishedAtField = $this->mapper->getPublishedAt();

        $publishedAt = new Carbon($this->row[is_string($publishedAtField) ? $publishedAtField : $publishedAtField[0]]);

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
            'categories' => $this->categories,
            'body' => $this->body,
            'published_at' => $this->publishedAt,
        ];
    }

    public function in(array $row): static
    {
        $this->row = $row;

        $this->title();
        $this->slug();
        $this->categories();
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

    public function resolveCategories(?array $categories = null): static
    {
        $this->resolveCategories = $categories;

        return $this;
    }

    public static function make($map): static
    {
        $article = new static();

        if (is_array($map)) {
            $article->mapper = new Mapper(...$map);
        } elseif ($map instanceof Mapper) {
            $article->mapper = $map;
        }

        return $article;
    }
}
