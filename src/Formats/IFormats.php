<?php

namespace Cable8mm\DbToMarkdown\Formats;

interface IFormats
{
    public function path(): string;

    public function render(): string;
}
