<?php

namespace App\Service\Email;

use App\Entity\Email;
use App\Service\Job\SendEmailJob;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailHandler implements MessageHandlerInterface
{
    protected $bus;
    protected $entityManager;
    protected $logger;
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
        LoggerInterface $logger,
        $maxTriesCount,
        $defaultFromName,
        $defaultFromEmail,
        $providers
    ) {
        $this->bus = $bus;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->maxTriesCount = $maxTriesCount;
        $this->defaultFromName = $defaultFromName;
        $this->defaultFromEmail = $defaultFromEmail;
        foreach ($providers as $provider) {
            $this->providers[] = new $provider();
        }
    }

    public function run(SendEmailJob $job)
    {
        $this->logger->info('Email job fired', [$job->getId()]);
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
                if ($isFinished = $this->batchSendViaProvider($provider, $email)) {
                    break;
                }
            }
            $emailRepository->markAsSent($email);
        } catch (\Exception $exception) {
            $this->logger->error('Queue failed: '.$exception->getMessage());

            return;
        }

        if (!$isFinished) {
            $this->logger->info('Put job again in queue', [$job->getId()]);
            $this->bus->dispatch($job);
        }
        $this->logger->info('Queue finished');
    }

    /**
     * @return bool
     *
     * @throws \Exception
     */
    protected function batchSendViaProvider(ProviderInterface $provider, Email $email)
    {
        $isFinished = true;
        foreach ($email->getRecipients() as $recipient) {
            if (!$recipient->isSent() && $recipient->getTryCount() < $this->maxTriesCount) {
                $recipient->increaseTriesCount();
                $succeed = $provider->send(
                    $email->getFromName(),
                    $email->getFromEmail(),
                    $recipient->getName(),
                    $recipient->getEmail(),
                    $email->getSubject(),
                    $email->getBody(),
                    $email->getBodyTextPart()
                );
                $this->logger->info('Sending email via provider', [
                    'to' => $recipient->getEmail(),
                    'tries' => $recipient->getTryCount(),
                    'provider' => $provider->getProviderName(),
                    'succeed' => $succeed,
                ]);
                $recipient->setIsSent($succeed);
                $recipient->setProvider($provider->getProviderName());
                $recipient->setSentAt(new \DateTime());
                $isFinished = $isFinished && $succeed;
            }
            if ($recipient->getTryCount() == $this->maxTriesCount) {
                // if the maximum try count exceeded then we shouldn't try to send email again
                $isFinished = true;
            }
        }

        return $isFinished;
    }

    public function __invoke(SendEmailJob $job)
    {
        $this->run($job);
    }
}
