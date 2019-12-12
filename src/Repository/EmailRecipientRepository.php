<?php

namespace App\Repository;

use App\Entity\EmailRecipient;
use Doctrine\ORM\EntityRepository;

class EmailRecipientRepository extends EntityRepository
{
    /**
     * @return EmailRecipient[]
     */
    public function findFailedRecipients(int $limit)
    {
        return $this->findBy(['status' => EmailRecipient::EMAIL_STATUS_FAILED], [], $limit);
    }

    /**
     * @return EmailRecipient[]
     */
    public function findPendingRecipients(int $limit)
    {
        return $this->findBy(['status' => EmailRecipient::EMAIL_STATUS_PENDING], [], $limit);
    }

    /**
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
     * @param $recipients
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
     * @param $recipients
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
