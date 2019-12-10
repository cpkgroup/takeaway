<?php

namespace App\Service\Email;

use App\Entity\Email;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;

class EmailRequestValidation
{
    protected $validator;

    public function __construct()
    {
        $this->validator = Validation::createValidator();
    }

    private function getConstraints()
    {
        return new Assert\Collection([
            'fromEmail' => new Assert\Optional([
                new Assert\Email(),
            ]),
            'fromName' => new Assert\Optional([
                new Assert\Type(['type' => 'string']),
            ]),
            'subject' => new Assert\NotBlank(),
            'body' => new Assert\NotBlank(),
            'bodyTextPart' => new Assert\Optional([
                new Assert\Type(['type' => 'string']),
            ]),
            'messageType' => new Assert\Optional([
                new Assert\Choice([
                    Email::EMAIL_TYPE_TEXT,
                    Email::EMAIL_TYPE_HTML,
                    Email::EMAIL_TYPE_MARKDOWN,
                ]),
            ]),
            'recipients' => [
                new Assert\Type('array'),
                new Assert\Count(['min' => 1]),
                new Assert\All([
                    new Assert\Collection([
                        'name' => new Assert\Optional([
                            new Assert\Type(['type' => 'string']),
                        ]),
                        'email' => [
                            new Assert\NotBlank(),
                            new Assert\Email(),
                        ],
                    ]),
                ]),
            ],
        ]);
    }

    public function validate($data): ConstraintViolationListInterface
    {
        $constraint = $this->getConstraints();

        return $this->validator->validate($data, $constraint);
    }
}
