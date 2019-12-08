<?php

namespace App\Repository;

use App\Entity\Email;
use App\Entity\EmailRecipient;
use Doctrine\ORM\EntityRepository;

class EmailRepository extends EntityRepository
{
    public function findEmail(int $id)
    {
        return $this->find($id);
    }

    /**
     * @return Email
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createEmail(array $data)
    {
        $email = new Email();
        if (isset($data['fromEmail'])) {
            $email->setFromEmail($data['fromEmail']);
        }
        if (isset($data['fromName'])) {
            $email->setFromName($data['fromName']);
        }
        $email->setSubject($data['subject']);
        if (isset($data['bodyTextPart'])) {
            $email->setBodyTextPart($data['bodyTextPart']);
        }
        $email->setBody($data['body']);
        foreach ($data['recipients'] as $item) {
            $recipient = new EmailRecipient();
            $recipient->setEmail($item['email']);
            if (isset($item['name'])) {
                $recipient->setName($item['name']);
            }
            $email->addRecipient($recipient);
        }

        $entityManager = $this->getEntityManager();
        $entityManager->persist($email);
        $entityManager->flush();

        return $email;
    }

    /**
     * @param string $provider
     * @param bool   $success
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function markAsSent(Email $email)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($email);
        $entityManager->flush();
    }
}
