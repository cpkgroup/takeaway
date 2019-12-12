<?php

namespace App\Command;

use App\Entity\Email;
use App\Service\Email\EmailRequest;
use App\Service\Job\SendEmailJob;
use App\Service\TextParser\TextParserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailCommand extends Command
{
    protected static $defaultName = 'app:send-email';
    protected $bus;
    protected $parser;
    protected $entityManager;

    public function __construct(
        MessageBusInterface $bus,
        TextParserInterface $parser,
        EntityManagerInterface $entityManager
    ) {
        $this->bus = $bus;
        $this->parser = $parser;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Compose a new email.')
            ->setHelp('This command allows you to send an Email.')
            ->addArgument('subject', InputArgument::REQUIRED, 'The email subject.')
            ->addArgument('recipient', InputArgument::REQUIRED, 'The email recipient.')
            ->addArgument('body', InputArgument::REQUIRED, 'The email body.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'Sending Email ...',
            '============',
            '',
        ]);

        $subject = $input->getArgument('subject');
        $recipient = $input->getArgument('recipient');
        $body = $input->getArgument('body');
        $messageContent = [
            'subject' => $subject,
            'body' => $body,
            'messageType' => Email::EMAIL_TYPE_TEXT,
            'recipients' => [
                [
                    'email' => $recipient,
                ],
            ],
        ];

        $emailRequest = new EmailRequest($this->parser, json_encode($messageContent));
        if (!$emailRequest->isValid()) {
            $output->writeln(json_encode($emailRequest->getErrors()));

            return 0;
        }

        $emailRepository = $this->entityManager->getRepository(Email::class);
        $email = $emailRepository->createEmail($emailRequest->getData());

        $emailJob = new SendEmailJob();
        $emailJob->setId($email->getId());

        // put to queue
        $this->bus->dispatch($emailJob);

        $output->writeln('Message successfully sent to: '.$recipient);

        return 0;
    }
}
