<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Recipient.
 *
 * @ORM\Table(name="recipient", indexes={@ORM\Index(name="status", columns={"status"})})
 * @ORM\Entity
 */
class Recipient
{
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
     * @ORM\Column(name="server_id", type="integer", nullable=false)
     */
    private $serverId = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="try_count", type="integer", nullable=false)
     */
    private $tryCount = 0;

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
     * @var Message
     *
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="recipients")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $message;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Recipient
    {
        $this->id = $id;

        return $this;
    }

    public function getServerId(): int
    {
        return $this->serverId;
    }

    public function setServerId(int $serverId): Recipient
    {
        $this->serverId = $serverId;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): Recipient
    {
        $this->status = $status;

        return $this;
    }

    public function getTryCount(): int
    {
        return $this->tryCount;
    }

    public function setTryCount(int $tryCount): Recipient
    {
        $this->tryCount = $tryCount;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Recipient
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Recipient
    {
        $this->email = $email;

        return $this;
    }

    public function getSentAt(): ?DateTime
    {
        return $this->sentAt;
    }

    public function setSentAt(?DateTime $sentAt): Recipient
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function setMessage(Message $message): Recipient
    {
        $this->message = $message;

        return $this;
    }
}
