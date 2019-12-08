<?php

namespace App\Service\Email;

use App\Entity\Email;
use App\Service\Job\SendEmailJob;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailHandler implements MessageHandlerInterface
{
    protected $bus;
    protected $entityManager;
    protected $maxTriesCount;
    protected $defaultFromName;
    protected $defaultFromEmail;
    /**
     * @var ProviderInterface[]
     */
    protected $providers = [];

    public function __construct(
        MessageBusInterface $bus,
        EntityManagerInterface $entityManager,
        $maxTriesCount,
        $defaultFromName,
        $defaultFromEmail,
        $providers
    ) {
        $this->bus = $bus;
        $this->entityManager = $entityManager;
        $this->maxTriesCount = $maxTriesCount;
        $this->defaultFromName = $defaultFromName;
        $this->defaultFromEmail = $defaultFromEmail;
        foreach ($providers as $provider) {
            $this->providers[] = new $provider();
        }
    }

    public function run(SendEmailJob $job)
    {
        echo 'email job fired#'.$job->getId()."\n";

        try {
            if (empty($this->providers)) {
                throw new \Exception('Email providers are empty!');
            }
            $emailRepository = $this->entityManager->getRepository(Email::class);

            /** @var Email $email */
            $email = $emailRepository->findEmail($job->getId());
            if (!$email->getFromName()) {
                $email->setFromName($this->defaultFromName);
            }
            if (!$email->getFromEmail()) {
                $email->setFromEmail($this->defaultFromEmail);
            }
            foreach ($this->providers as $provider) {
                // if provider could sending email break the loop, else try next provider
                if ($succeed = $this->batchSendViaProvider($provider, $email)) {
                    break;
                }
            }
            $emailRepository->markAsSent($email);
        } catch (\Exception $exception) {
            // log error here

            return;
        }

        if (!$succeed) {
            echo 'put job again in queue#'.$job->getId()."\n";
            $this->bus->dispatch($job);
        }
        echo "queue finished \n\n\n";
    }

    /**
     * @return bool
     *
     * @throws \Exception
     */
    protected function batchSendViaProvider(ProviderInterface $provider, Email $email)
    {
        $succeedAll = true;
        foreach ($email->getRecipients() as $recipient) {
            if (!$recipient->isSent() && $recipient->getTryCount() < $this->maxTriesCount) {
                $recipient->increaseTriesCount();
                echo 'sending email to: '.$recipient->getEmail().'#'.$provider->getProviderName().'#'.$recipient->getTryCount().'#';
                $succeed = $provider->send(
                    $email->getFromName(),
                    $email->getFromEmail(),
                    $recipient->getName(),
                    $recipient->getEmail(),
                    $email->getSubject(),
                    $email->getBody(),
                    $email->getBodyTextPart()
                );
                $recipient->setIsSent($succeed);
                $recipient->setProvider($provider->getProviderName());
                $recipient->setSentAt(new \DateTime());
                $succeedAll = $succeedAll && $succeed;
                echo $succeed ? 'success' : 'failed';
                echo "\n";
            }
        }

        return $succeedAll;
    }

    public function __invoke(SendEmailJob $job)
    {
        $this->run($job);
    }
}
