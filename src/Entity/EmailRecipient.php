<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * EmailRecipient.
 *
 * @ORM\Table(name="email_recipient", indexes={@ORM\Index(name="status", columns={"status"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\EmailRecipientRepository")
 */
class EmailRecipient
{
    const EMAIL_STATUS_PENDING = 0;
    const EMAIL_STATUS_SENT = 1;
    const EMAIL_STATUS_FAILED = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = self::EMAIL_STATUS_PENDING;

    /**
     * @var int
     *
     * @ORM\Column(name="try_count", type="integer", nullable=false)
     */
    private $tryCount = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="provider", type="string", length=255, nullable=true)
     */
    private $provider;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="sent_at", type="datetime", nullable=true)
     */
    private $sentAt;

    /**
     * @var Email
     *
     * @ORM\ManyToOne(targetEntity="Email", inversedBy="recipients")
     * @ORM\JoinColumn(name="email_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $emailMessage;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    public function getTryCount(): int
    {
        return $this->tryCount;
    }

    public function setTryCount(int $tryCount)
    {
        $this->tryCount = $tryCount;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function setProvider(?string $provider)
    {
        $this->provider = $provider;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name)
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function getSentAt(): ?DateTime
    {
        return $this->sentAt;
    }

    public function setSentAt(?DateTime $sentAt)
    {
        $this->sentAt = $sentAt;
    }

    public function getEmailMessage(): Email
    {
        return $this->emailMessage;
    }

    public function setEmailMessage(Email $emailMessage)
    {
        $this->emailMessage = $emailMessage;
    }

    public function increaseTriesCount()
    {
        ++$this->tryCount;
    }

    public function setIsSent(bool $isSent)
    {
        $this->status = $isSent ? self::EMAIL_STATUS_SENT : self::EMAIL_STATUS_FAILED;
    }

    public function isSent(): bool
    {
        return self::EMAIL_STATUS_SENT == $this->status;
    }

    public function isPending(): bool
    {
        return self::EMAIL_STATUS_PENDING == $this->status;
    }
}
