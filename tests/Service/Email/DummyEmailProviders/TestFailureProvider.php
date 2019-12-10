<?php

namespace App\Tests\Service\Email\DummyEmailProviders;

use App\Service\Email\ProviderInterface;

class TestFailureProvider implements ProviderInterface
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

        return false;
    }

    public function getProviderName(): string
    {
        return 'Failure';
    }
}
