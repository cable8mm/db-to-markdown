<?php

namespace Cable8mm\DbToMarkdown\Formats;

use Carbon\Carbon;

class Markdown implements IFormats
{
    public function __construct(
        private Carbon $date,
        private string $body,
        private string $slug,
    ) {

    }

    public function path(string $extention = 'md'): string
    {
        $prefix = $this->date->format('Y-m-d');

        $filename = preg_replace('/[_ ]/', '-', $this->slug);

        return $prefix.'-'.$filename.'.'.$extention;
    }

    public function render(): string
    {
        return $this->body;
    }
}
