<?php

namespace App\Tests\Service\Email;

use App\Entity\Email;
use App\Service\Email\EmailRequest;
use App\Service\TextParser\MarkdownTextParser;
use PHPUnit\Framework\TestCase;

class EmailRequestTest extends TestCase
{
    /**
     * @var EmailRequest
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
     * test email request here.
     *
     * @dataProvider sampleRequests
     */
    public function testEmailRequest(array $data, ?string $formattedBody, int $numberOfErrors)
    {
        $emailRequest = new EmailRequest($this->parser, json_encode($data, true));

        self::assertSame($numberOfErrors, count($emailRequest->getErrors()));
        if ($formattedBody) {
            self::assertSame($formattedBody, $emailRequest->getData()['body']);
        }
    }

    /**
     * @return array
     */
    public function sampleRequests()
    {
        return [
            [
                [],
                null,
                1,
            ],
            [
                [
                    'subject' => 'hi',
                ],
                null,
                2,
            ],
            [
                [
                    'subject' => 'hi',
                    'body' => 'Hello world',
                ],
                null,
                1,
            ],
            [
                [
                    'subject' => 'hi1',
                    'body' => '**Hello world**',
                    'messageType' => Email::EMAIL_TYPE_MARKDOWN,
                    'recipients' => [
                        [
                            'email' => 'habibi.mh@gmail.com',
                            'name' => 'mohamad',
                        ],
                    ],
                ],
                '<p><strong>Hello world</strong></p>',
                0,
            ],
            [
                [
                    'subject' => 'hi2',
                    'body' => '<p><strong>Hello world</strong></p>',
                    'messageType' => Email::EMAIL_TYPE_TEXT,
                    'recipients' => [
                        [
                            'email' => 'habibi.mh@gmail.com',
                            'name' => 'mohamad',
                        ],
                    ],
                ],
                '&lt;p&gt;&lt;strong&gt;Hello world&lt;/strong&gt;&lt;/p&gt;',
                0,
            ],
            [
                [
                    'subject' => 'hi3',
                    'body' => '<p><strong>Hello world</strong></p>',
                    'messageType' => Email::EMAIL_TYPE_HTML,
                    'recipients' => [
                        [
                            'email' => 'habibi.mh@gmail.com',
                            'name' => 'mohamad',
                        ],
                    ],
                ],
                '<p><strong>Hello world</strong></p>',
                0,
            ],
            [
                [
                    'subject' => 'hi4',
                    'body' => 'Hello world',
                    'messageType' => 345,
                    'recipients' => [
                        [
                            'email' => 'habibi.mh_gmail.com',
                        ],
                    ],
                ],
                null,
                2,
            ],
        ];
    }
}
