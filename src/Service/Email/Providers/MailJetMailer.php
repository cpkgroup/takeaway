<?php

namespace App\Service\Email\Providers;

use App\Service\Email\ProviderInterface;
use App\Service\Email\ProviderParamsTrait;
use Exception;

class MailJetMailer implements ProviderInterface
{
    use ProviderParamsTrait;

    public function send(
        ?string $fromName,
        string $fromEmail,
        ?string $toName,
        string $toEmail,
        string $subject,
        string $body,
        ?string $bodyTextPart
    ): bool {
        if (!$this->data['active']) {
            return false;
        }

        // the mailer jet email provider doesn't allow to have dynamic sender email
        // so I have to ignore $fromEmail and use staticSender on this provider.
        $data = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $this->data['staticSender'],
                        'Name' => $fromName,
                    ],
                    'To' => [
                        [
                            'Email' => $toEmail,
                            'Name' => $toName,
                        ],
                    ],
                    'Subject' => $subject,
                    'TextPart' => $bodyTextPart,
                    'HTMLPart' => $body,
                ],
            ],
        ];

        $response = $this->httpClient->request('POST', $this->data['uri'], [
            'json' => $data,
            'auth_basic' => [$this->data['apiKeyPublic'], $this->data['apiKeyPrivate']],
        ]);

        if (200 == $response->getStatusCode()) {
            return true;
        }
        throw new Exception($response->getStatusCode().': '.$response->getContent(false));
    }

    public function getProviderName(): string
    {
        return 'MailJet';
    }
}
