<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class LinkController extends AbstractController
{
    /**
     * @Route("/api/link", name="link")
     */
    public function index()
    {
        return new JsonResponse;
    }

    /**
     * @Route("/anylink", name="anylink")
     */
    public function any()
    {
        return new JsonResponse;
    }
}
