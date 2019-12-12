<?php

namespace App\Repository;

use App\Entity\EmailRecipient;
use Doctrine\ORM\EntityRepository;

class EmailRecipientRepository extends EntityRepository
{
    /**
     * Retrieve all failed recipients.
     *
     * @return EmailRecipient[]
     */
    public function findFailedRecipients(int $limit)
    {
        return $this->findBy(['status' => EmailRecipient::EMAIL_STATUS_FAILED], [], $limit);
    }

    /**
     * Retrieve all pending recipients.
     *
     * @return EmailRecipient[]
     */
    public function findPendingRecipients(int $limit)
    {
        return $this->findBy(['status' => EmailRecipient::EMAIL_STATUS_PENDING], [], $limit);
    }

    /**
     * Reset status and try count of a recipient.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function resetRecipient(EmailRecipient $emailRecipient)
    {
        $emailRecipient->setStatus(EmailRecipient::EMAIL_STATUS_PENDING);
        $emailRecipient->setTryCount(0);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($emailRecipient);
        $entityManager->flush();
    }

    /**
     * Reset status and try count of array of recipient.
     *
     * @param array $recipients
     *
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function resetRecipients($recipients)
    {
        /** @var EmailRecipient $recipient */
        foreach ($recipients as $recipient) {
            $this->resetRecipient($recipient);
        }
    }

    /**
     * Get unique email ids in an array of recipients.
     *
     * @param array $recipients
     *
     * @return array
     */
    public function getUniqueEmailIdsFromRecipients($recipients)
    {
        $emailIds = [];
        /** @var EmailRecipient $recipient */
        foreach ($recipients as $recipient) {
            $emailIds[] = $recipient->getEmailMessage()->getId();
        }

        return array_unique($emailIds);
    }
}
