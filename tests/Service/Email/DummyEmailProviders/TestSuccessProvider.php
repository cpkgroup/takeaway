<?php

namespace App\Tests\Service\Email\DummyEmailProviders;

use App\Service\Email\ProviderInterface;

class TestSuccessProvider implements ProviderInterface
{
    public function send(
        ?string $fromName,
        string $fromEmail,
        ?string $toName,
        string $toEmail,
        string $subject,
        string $body,
        ?string $bodyTextPart
    ): bool {

        return true;
    }

    public function getProviderName(): string
    {
        return 'Success';
    }
}
