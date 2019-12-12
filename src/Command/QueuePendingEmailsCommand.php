<?php

namespace App\Command;

use App\Entity\EmailRecipient;
use App\Service\Job\SendEmailJob;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class QueuePendingEmailsCommand extends Command
{
    protected static $defaultName = 'app:queue-pending-emails';
    protected $bus;
    protected $entityManager;

    public function __construct(
        MessageBusInterface $bus,
        EntityManagerInterface $entityManager
    ) {
        $this->bus = $bus;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Queue pending emails')
            ->setHelp('This command allows you to queue pending emails')
            ->addArgument('limit', InputArgument::OPTIONAL, 'The email limit');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit = $input->getArgument('limit') ?? 100;
        $output->writeln([
            'Queue Maximum '.$limit.' Pending Emails ...',
            '============',
            '',
        ]);

        $emailRecipientRepository = $this->entityManager->getRepository(EmailRecipient::class);
        $recipients = $emailRecipientRepository->findPendingRecipients($limit);
        $emailIds = $emailRecipientRepository->getUniqueEmailIdsFromRecipients($recipients);
        foreach ($emailIds as $emailId) {
            $emailJob = new SendEmailJob();
            $emailJob->setId($emailId);

            // put to queue
            $this->bus->dispatch($emailJob);
        }

        $output->writeln(count($emailIds).' Messages successfully queued again.');

        return 0;
    }
}
