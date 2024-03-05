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
    ) {

    }

    public function path(string $extention = 'markdown'): string
    {
        $prefix = $this->date->format('Y-m-d');

        $filename = preg_replace('/[_ ]/', '-', $this->slug);

        return $prefix.'-'.$filename.'.'.$extention;
    }

    public function render(): string
    {
        $out = '---'.PHP_EOL;
        $out .= 'layout : '.$this->layout.PHP_EOL;
        $out .= 'title : "'.$this->title.'"'.PHP_EOL;
        $out .= 'date : '.$this->date->format('Y-m-d h:i:s').PHP_EOL;

        if (! is_null($this->categories)) {
            $out .= 'categories : '.$this->categories.PHP_EOL;
        }

        $out .= 'author : '.$this->author.PHP_EOL;
        $out .= '---'.PHP_EOL;

        $out .= $this->body;

        return $out;
    }
}
