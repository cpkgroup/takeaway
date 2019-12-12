<?php

namespace App\Service\Email;

use App\Entity\Email;
use App\Service\Job\SendEmailJob;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EmailHandler implements MessageHandlerInterface
{
    protected $bus;
    protected $entityManager;
    protected $logger;
    protected $httpClient;
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
        HttpClientInterface $httpClient,
        $maxTriesCount,
        $defaultFromName,
        $defaultFromEmail,
        $providers
    ) {
        $this->bus = $bus;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->maxTriesCount = $maxTriesCount;
        $this->defaultFromName = $defaultFromName;
        $this->defaultFromEmail = $defaultFromEmail;
        foreach ($providers as $provider => $data) {
            $this->providers[] = new $provider($this->httpClient, $data);
        }
    }

    /**
     * Run an email job, sending emails via providers here.
     */
    public function run(SendEmailJob $job)
    {
        $this->logger->info('Email job fired', [$job->getId()]);

        if (empty($this->providers)) {
            $this->logger->error('Email providers are empty!');

            return;
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
            try {
                // if provider could sending email break the loop, else try next provider
                if ($isFinished = $this->batchSendViaProvider($provider, $email)) {
                    break;
                }
            } catch (\Exception $exception) {
                $this->logger->error('Provider ['.$provider->getProviderName().'] failed: '.$exception->getMessage());
            }
        }
        $emailRepository->markAsSent($email);

        if (!$isFinished) {
            $this->logger->info('Put job again in queue', [$job->getId()]);
            $this->bus->dispatch($job);
        }
        $this->logger->info('Email job finished');
    }

    /**
     * This method send email to all of the recipients of its via provider.
     *
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
