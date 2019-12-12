<?php

namespace App\Repository;

use App\Entity\Email;
use App\Entity\EmailRecipient;
use Doctrine\ORM\EntityRepository;

class EmailRepository extends EntityRepository
{
    /**
     * Retrieve an email by id.
     *
     * @return object|null
     */
    public function findEmail(int $id)
    {
        return $this->find($id);
    }

    /**
     * Retrieve emails by desc order, using limit and offset.
     *
     * @return array
     */
    public function findAllEmail(int $limit, int $offset)
    {
        return $this->findBy([], ['id' => 'DESC'], $limit, $offset);
    }

    /**
     * Count all the emails.
     *
     * @return int
     */
    public function countAllEmail()
    {
        return $this->count([]);
    }

    /**
     * Create a new email with the data from EmailRequest class.
     *
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
     * Mark an email as send and save in database.
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
