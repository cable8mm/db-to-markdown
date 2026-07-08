<?php

namespace Cable8mm\DbToMarkdown\Formats;

use Carbon\Carbon;

class Markdown implements IFormat
{
    public function __construct(
        private Carbon $date,
        private string $body,
        private string $slug,
    ) {}

    public function path(string $extension = 'md'): string
    {
        $prefix = $this->date->format('Y-m-d');

        $filename = preg_replace('/[_ ]/', '-', $this->slug);

        return $prefix.'-'.$filename.'.'.$extension;
    }

    public function render(): string
    {
        return $this->body;
    }
}
