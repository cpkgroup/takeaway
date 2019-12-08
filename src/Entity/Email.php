<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Email Entity Class.
 *
 * @ORM\Table(name="email")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Email
{
    const EMAIL_TYPE_TEXT = 0;
    const EMAIL_TYPE_HTML = 1;
    const EMAIL_TYPE_MARKDOWN = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="EmailRecipient", mappedBy="emailMessage", cascade={"persist"})
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
     * @var string|null
     *
     * @ORM\Column(name="body_text_part", type="text", length=65535, nullable=true)
     */
    private $bodyTextPart;

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

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getRecipients()
    {
        return $this->recipients;
    }

    public function setRecipients(ArrayCollection $recipients)
    {
        $this->recipients = $recipients;
    }

    public function getFromEmail(): ?string
    {
        return $this->fromEmail;
    }

    public function setFromEmail(?string $fromEmail)
    {
        $this->fromEmail = $fromEmail;
    }

    public function getFromName(): ?string
    {
        return $this->fromName;
    }

    public function setFromName(?string $fromName)
    {
        $this->fromName = $fromName;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject)
    {
        $this->subject = $subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body)
    {
        $this->body = $body;
    }

    /**
     * @return string|null
     */
    public function getBodyTextPart(): ?string
    {
        return $this->bodyTextPart;
    }

    /**
     * @param string|null $bodyTextPart
     */
    public function setBodyTextPart(?string $bodyTextPart): void
    {
        $this->bodyTextPart = $bodyTextPart;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return Email
     */
    public function addRecipient(EmailRecipient $recipient)
    {
        $recipient->setEmailMessage($this);
        $this->recipients[] = $recipient;
    }
}
