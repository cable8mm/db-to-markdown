<?php

namespace Cable8mm\DbToMarkdown\Formats;

interface IFormat
{
    public function path(?string $extension = null): string;

    public function render(): string;

    public function getExtension(): string;
}
