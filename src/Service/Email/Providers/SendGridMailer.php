<?php

namespace App\Service\Email\Providers;

use App\Service\Email\ProviderInterface;
use App\Service\Email\ProviderParamsTrait;
use Exception;

class SendGridMailer implements ProviderInterface
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
        $content = [];

        if ($bodyTextPart) {
            $content[] = [
                'type' => 'text/plain',
                'value' => $bodyTextPart,
            ];
        }
        $content[] = [
            'type' => 'text/html',
            'value' => $body,
        ];

        $data = [
            'personalizations' => [
                [
                    'to' => [
                        [
                            'email' => $toEmail,
                            'name' => $toName,
                        ],
                    ],
                    'subject' => $subject,
                ],
            ],
            'content' => $content,
            'from' => [
                'email' => $fromEmail,
                'name' => $fromName,
            ],
        ];

        $response = $this->httpClient->request('POST', $this->data['uri'], [
            'json' => $data,
            'auth_bearer' => $this->data['apiKey'],
        ]);

        // status code 2xx is accepted
        if (200 <= $response->getStatusCode() && 300 > $response->getStatusCode()) {
            return true;
        }
        throw new Exception($response->getStatusCode().': '.$response->getContent(false));
    }

    public function getProviderName(): string
    {
        return 'SendGrid';
    }
}
