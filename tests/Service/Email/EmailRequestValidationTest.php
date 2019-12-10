<?php

namespace App\Tests\Service\Email;

use App\Entity\Email;
use App\Service\Email\EmailRequestValidation;
use PHPUnit\Framework\TestCase;

class EmailRequestValidationTest extends TestCase
{
    /**
     * @var EmailRequestValidation
     */
    protected $validation;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->validation = new EmailRequestValidation();
    }

    /**
     * Test validation request here.
     *
     * @dataProvider sampleRequests
     */
    public function testParse(array $request, int $numberOfErrors)
    {
        $violations = $this->validation->validate($request);
        self::assertSame($numberOfErrors, $violations->count());
    }

    /**
     * @return array
     */
    public function sampleRequests()
    {
        return [
            [
                [],
                3,
            ],
            [
                [
                    'subject' => 'hi',
                ],
                2,
            ],
            [
                [
                    'subject' => 'hi',
                    'body' => 'Hello world',
                ],
                1,
            ],
            [
                [
                    'subject' => 'hi1',
                    'body' => 'Hello world',
                    'recipients' => 'habibi.mh@gmail.com',
                ],
                3,
            ],
            [
                [
                    'subject' => 'hi2',
                    'body' => 'Hello world',
                    'recipients' => [
                        [
                            'email' => 'habibi.mh@gmail.com',
                        ],
                    ],
                ],
                0,
            ],
            [
                [
                    'subject' => 'hi3',
                    'body' => 'Hello world',
                    'recipients' => [
                        [
                            'email' => 'habibi.mh@gmail.com',
                            'first_name' => 'mohamad',
                        ],
                    ],
                ],
                1,
            ],
            [
                [
                    'subject' => 'hi4',
                    'body' => 'Hello world',
                    'messageType' => Email::EMAIL_TYPE_MARKDOWN,
                    'recipients' => [
                        [
                            'email' => 'habibi.mh@gmail.com',
                            'name' => 'mohamad',
                        ],
                    ],
                ],
                0,
            ],
            [
                [
                    'subject' => 'hi5',
                    'body' => 'Hello world',
                    'messageType' => 345,
                    'recipients' => [
                        [
                            'email' => 'habibi.mh_gmail.com',
                        ],
                    ],
                ],
                2,
            ],
            [
                [
                    'subject' => 'hi6',
                    'body2' => 'Hello world',
                    'recipients' => [
                        [
                            'email' => 'habibi.mh_gmail.com',
                        ],
                    ],
                ],
                3,
            ],
        ];
    }
}
