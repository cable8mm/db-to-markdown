<?php

namespace Cable8mm\DbToMarkdown\Formats;

interface IFormat
{
    public function path(): string;

    public function render(): string;
}
