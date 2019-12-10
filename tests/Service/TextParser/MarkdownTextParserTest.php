<?php

namespace App\Tests\Service\TextParser;

use App\Service\TextParser\MarkdownTextParser;
use PHPUnit\Framework\TestCase;

class MarkdownTextParserTest extends TestCase
{
    /**
     * @var MarkdownTextParser
     */
    protected $parser;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->parser = new MarkdownTextParser();
    }

    /**
     * Test parse here.
     *
     * @dataProvider markDownData
     */
    public function testParse(string $markdown, string $html)
    {
        self::assertSame($html, $this->parser->parse($markdown));
    }

    /**
     * @return array
     */
    public function markDownData()
    {
        return [
            ['hi', '<p>hi</p>'],
            ['_welcome_!', '<p><em>welcome</em>!</p>'],
            ['**This is bold text**', '<p><strong>This is bold text</strong></p>'],
            ['# h1 Heading', '<h1>h1 Heading</h1>'],
            ['`Some codes here`', '<p><code>Some codes here</code></p>'],
            ['- __[google](https://google.com/)__ - google web site', "<ul>\n<li><strong><a href=\"https://google.com/\">google</a></strong> - google web site</li>\n</ul>"],
        ];
    }
}
