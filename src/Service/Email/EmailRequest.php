<?php

namespace App\Service\Email;

use App\Entity\Email;
use App\Service\TextParser\TextParserInterface;

class EmailRequest
{
    /**
     * @var array
     */
    private $data;
    private $validationErrors = [];

    /**
     * EmailRequest constructor.
     *
     * @param $data
     */
    public function __construct(TextParserInterface $parser, string $data)
    {
        $this->data = json_decode($data, true);
        if (!$this->data) {
            $this->validationErrors[] = ['error' => 'Invalid Json format'];
            return;
        }
        $validation = new EmailRequestValidation();
        $violations = $validation->validate($this->data);
        foreach ($violations as $violation) {
            $this->validationErrors[] = [$violation->getPropertyPath() => $violation->getMessage()];
        }
        if ($this->isValid()) {
            $this->formatMessageBody($parser);
        }
    }

    public function isValid()
    {
        return empty($this->validationErrors);
    }

    public function getErrors()
    {
        return $this->validationErrors;
    }

    public function getData()
    {
        return $this->data;
    }

    public function formatMessageBody(TextParserInterface $parser)
    {
        if (isset($this->data['messageType'])) {
            switch ($this->data['messageType']) {
                case Email::EMAIL_TYPE_MARKDOWN:
                    $this->data['body'] = $parser->parse($this->data['body']);
                    break;
                case Email::EMAIL_TYPE_TEXT:
                    $this->data['body'] = htmlentities($this->data['body']);
                    break;
            }
        }
    }
}
