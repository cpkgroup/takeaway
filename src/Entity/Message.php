<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Message.
 *
 * @ORM\Table(name="message")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Message
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
     * @ORM\OneToMany(targetEntity="Recipient", mappedBy="message", cascade={"persist"})
     */
    private $recipients;

    /**
     * @var string|null
     *
     * @ORM\Column(name="from_email", type="string", length=255, nullable=true)
     */
    private $fromEmail;

    /**
     * @var string|null
     *
     * @ORM\Column(name="from_name", type="string", length=255, nullable=true)
     */
    private $fromName;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255, nullable=false)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", length=65535, nullable=false)
     */
    private $body;

    /**
     * @var int
     *
     * @ORM\Column(name="message_type", type="integer", nullable=false)
     */
    private $messageType = 0;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * Message constructor.
     */
    public function __construct()
    {
        $this->recipients = new ArrayCollection();
    }

    /**
     * Gets triggered only on insert.
     *
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Message
    {
        $this->id = $id;

        return $this;
    }

    public function getRecipients(): ArrayCollection
    {
        return $this->recipients;
    }

    public function setRecipients(ArrayCollection $recipients): Message
    {
        $this->recipients = $recipients;

        return $this;
    }

    public function getFromEmail(): ?string
    {
        return $this->fromEmail;
    }

    public function setFromEmail(?string $fromEmail): Message
    {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    public function getFromName(): ?string
    {
        return $this->fromName;
    }

    public function setFromName(?string $fromName): Message
    {
        $this->fromName = $fromName;

        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): Message
    {
        $this->subject = $subject;

        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): Message
    {
        $this->body = $body;

        return $this;
    }

    public function getMessageType(): int
    {
        return $this->messageType;
    }

    public function setMessageType(int $messageType): Message
    {
        $this->messageType = $messageType;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): Message
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Message
     */
    public function addRecipient(Recipient $recipient)
    {
        $recipient->setMessage($this);
        $this->recipients[] = $recipient;

        return $this;
    }
}
