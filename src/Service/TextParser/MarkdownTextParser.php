<?php

namespace App\Service\TextParser;

use Parsedown;

class MarkdownTextParser implements TextParserInterface
{
    protected $parsedown;

    public function __construct()
    {
        $this->parsedown = new Parsedown();
    }

    public function parse(string $text)
    {
        return $this->parsedown->text($text);
    }
}
