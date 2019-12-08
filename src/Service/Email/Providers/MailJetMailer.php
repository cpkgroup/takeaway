<?php

namespace App\Service\Email\Providers;

use App\Service\Email\ProviderInterface;

class MailJetMailer implements ProviderInterface
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
        //@TODO implement this
        return 1 == rand(0, 5) ? true : false;
    }

    public function getProviderName(): string
    {
        return 'MailJet';
    }
}
