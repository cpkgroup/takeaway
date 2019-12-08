<?php

namespace App\Controller;

use App\Entity\Email;
use App\Service\Job\SendEmailJob;
use App\Service\Email\EmailRequest;
use App\Service\TextParser\TextParserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailController extends AbstractController
{
    /**
     * @return string
     */
    public function composeAction(
        Request $request,
        MessageBusInterface $bus,
        TextParserInterface $parser
    ) {
        $emailRequest = new EmailRequest($parser, $request->getContent());
        if (!$emailRequest->isValid()) {
            return new JsonResponse([
                'status' => 'failed',
                'errors' => $emailRequest->getErrors(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        $emailRepository = $this->getDoctrine()->getRepository(Email::class);
        $email = $emailRepository->createEmail($emailRequest->getData());

        $emailJob = new SendEmailJob();
        $emailJob->setId($email->getId());

        // put to queue
        $bus->dispatch($emailJob);

        return new JsonResponse([
            'status' => 'succeed',
        ], JsonResponse::HTTP_ACCEPTED);
    }

    public function listAction(Request $request)
    {
    }
}
