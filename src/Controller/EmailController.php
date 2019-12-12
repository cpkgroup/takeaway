<?php

namespace App\Controller;

use App\Entity\Email;
use App\Service\Email\EmailRequest;
use App\Service\Job\SendEmailJob;
use App\Service\TextParser\TextParserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailController extends AbstractController
{
    /**
     * Send new email by a json formatted request.
     *
     * @return JsonResponse
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

    /**
     * Find all email logs and sort them desc by default,
     * The result will paginate with page param,
     * And The limit of the result is defined on services.yaml.
     *
     * @return JsonResponse
     */
    public function listAction(Request $request)
    {
        $limit = $this->getParameter('reportLimit');
        $page = (int) $request->get('page', 1);
        $offset = ($page - 1) * $limit;
        $emailRepository = $this->getDoctrine()->getRepository(Email::class);
        $emails = $emailRepository->findAllEmail($limit, $offset);

        $countAll = $emailRepository->countAllEmail();

        return new JsonResponse([
            'items' => $emails,
            'numberOfPages' => ceil($countAll / $limit),
        ]);
    }
}
