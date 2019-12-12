<?php

namespace App\Tests\Service\Email;

use App\Entity\Email;
use App\Entity\EmailRecipient;
use App\Repository\EmailRepository;
use App\Service\Email\EmailHandler;
use App\Service\Job\SendEmailJob;
use App\Tests\Service\Email\DummyEmailProviders\TestFailureProvider;
use App\Tests\Service\Email\DummyEmailProviders\TestFailureProvider2;
use App\Tests\Service\Email\DummyEmailProviders\TestFailureProvider3;
use App\Tests\Service\Email\DummyEmailProviders\TestSuccessProvider;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EmailHandlerTest extends TestCase
{
    /**
     * @var MessageBusInterface
     */
    protected $bus;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    protected $defaultFromName = 'Test';
    protected $defaultFromEmail = 'tester@localhost.com';

    /**
     * @var SendEmailJob
     */
    protected $emailJob;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->emailJob = new SendEmailJob();
        $this->emailJob->setId(1);

        $this->bus = $this->createMock(MessageBusInterface::class);

        $this->bus
            ->method('dispatch')
            ->with(self::isInstanceOf(SendEmailJob::class))
            ->willReturn(new Envelope($this->emailJob));

        $email = new Email();
        $email->setSubject('Hello');
        $email->setBody('testing email');

        $recipient1 = new EmailRecipient();
        $recipient1->setEmail('test1@test.com');
        $email->addRecipient($recipient1);

        $recipient2 = new EmailRecipient();
        $recipient2->setEmail('test2@test.com');
        $email->addRecipient($recipient2);

        $emailRepository = $this->createMock(EmailRepository::class);
        $emailRepository->expects($this->any())
            ->method('findEmail')
            ->willReturn($email);

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($emailRepository);

        $this->logger = $this->createMock(LoggerInterface::class);
        $this->httpClient = $this->createMock(HttpClientInterface::class);
    }

    /**
     * Test email handler here.
     *
     * @dataProvider handlerData
     */
    public function testEmailHandler(
        array $providers,
        int $maxTriesCount,
        int $triesCount,
        int $queueCount,
        bool $isSent
    ) {
        $this->bus
            ->expects(self::exactly($queueCount))
            ->method('dispatch')
            ->with(self::isInstanceOf(SendEmailJob::class))
            ->willReturn(new Envelope($this->emailJob));

        $emailRepo = $this->entityManager->getRepository(Email::class);
        /** @var Email $email */
        $email = $emailRepo->findEmail(1);
        $recipients = $email->getRecipients();

        self::assertNull($email->getFromName());
        self::assertNull($email->getFromEmail());

        foreach ($recipients as $recipient) {
            self::assertSame($recipient->getTryCount(), 0);
            self::assertSame($recipient->isPending(), true);
            self::assertNull($recipient->getSentAt());
            self::assertNull($recipient->getProvider());
        }
        for ($i = 0; $i < $queueCount + 2; ++$i) {
            $emailHandler = new EmailHandler(
                $this->bus,
                $this->entityManager,
                $this->logger,
                $this->httpClient,
                $maxTriesCount,
                $this->defaultFromName,
                $this->defaultFromEmail,
                $providers
            );
            $emailHandler->run($this->emailJob);
        }

        self::assertSame($email->getFromName(), $this->defaultFromName);
        self::assertSame($email->getFromEmail(), $this->defaultFromEmail);

        foreach ($recipients as $recipient) {
            self::assertSame($recipient->getTryCount(), $triesCount);
            self::assertSame($recipient->isSent(), $isSent);
            self::assertSame($recipient->getProvider(), $isSent ? 'Success' : 'Failure');
            self::assertNotNull($recipient->getSentAt());
        }
    }

    public function handlerData()
    {
        return [
            [
                [
                    TestFailureProvider::class => [],
                    TestSuccessProvider::class => [],
                ],
                2,
                2,
                0,
                true,
            ],
            [
                [
                    TestFailureProvider::class => [],
                    TestSuccessProvider::class => [],
                ],
                1,
                1,
                0,
                false,
            ],
            [
                [
                    TestSuccessProvider::class => [],
                    TestFailureProvider::class => [],
                ],
                3,
                1,
                0,
                true,
            ],
            [
                [
                    TestFailureProvider::class => [],
                    TestFailureProvider2::class => [],
                    TestFailureProvider3::class => [],
                    TestSuccessProvider::class => [],
                ],
                30,
                4,
                0,
                true,
            ],
            [
                [
                    TestFailureProvider::class => [],
                    TestFailureProvider2::class => [],
                    TestFailureProvider3::class => [],
                ],
                10,
                10,
                3,
                false,
            ],
        ];
    }
}
