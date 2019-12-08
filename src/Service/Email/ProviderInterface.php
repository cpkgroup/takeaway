<?php

namespace App\Service\Email;

interface ProviderInterface
{
    public function send(
        ?string $fromName,
        string $fromEmail,
        ?string $toName,
        string $toEmail,
        string $subject,
        string $body,
        ?string $bodyTextPart
    ): bool;

    public function getProviderName(): string;
}
