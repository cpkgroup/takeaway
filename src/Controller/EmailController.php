<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EmailController extends AbstractController
{
    public function composeAction(Request $request)
    {
        return new JsonResponse([
            'status' => 'succeed',
        ], JsonResponse::HTTP_ACCEPTED);
    }

    public function listAction(Request $request)
    {
    }
}
