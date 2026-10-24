<?php

namespace Cable8mm\DbToMarkdown\Formats;

use Carbon\Carbon;

class Jekyll implements IFormat
{
    public function __construct(
        private string $layout,
        private string $title,
        private Carbon $date,
        private string $author,
        private string $body,
        private string $slug,
        private ?string $categories = null
    ) {}

    public function getExtension(): string
    {
        return 'markdown';
    }

    public function path(?string $extension = null): string
    {
        $ext = $extension ?? $this->getExtension();

        $prefix = $this->date->format('Y-m-d');

        $filename = preg_replace('/[^\pL\pN-]+/u', '-', $this->slug) ?? '';
        $filename = trim($filename, '-_') ?: 'untitled';

        return $prefix.'-'.$filename.'.'.$ext;
    }

    public function render(): string
    {
        $out = '---'.PHP_EOL;
        $out .= 'layout: '.$this->layout.PHP_EOL;
        $out .= 'title: '.self::yamlString($this->title, true).PHP_EOL;
        $out .= 'date: '.$this->date->format('Y-m-d H:i:s').PHP_EOL;

        if (! is_null($this->categories)) {
            $out .= 'categories: '.self::yamlString($this->categories).PHP_EOL;
        }

        $out .= 'author: '.self::yamlString($this->author).PHP_EOL;
        $out .= '---'.PHP_EOL;

        $out .= $this->body;

        return $out;
    }

    private static function yamlString(string $value, bool $forceQuotes = false): string
    {
        $plainScalarIsSafe = ! $forceQuotes
            && preg_match('/\A[\pL\pN _-]+\z/u', $value) === 1
            && ! preg_match('/\A(?:null|true|false|yes|no|on|off|[-+]?[0-9].*)\z/i', $value);

        if ($plainScalarIsSafe) {
            return $value;
        }

        // JSON strings are valid YAML double-quoted scalars; this safely escapes
        // quotes, line breaks, and other control characters from source data.
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    }
}
