<?php

namespace App\Controller\About;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends AbstractController
{
    #[Route('/apropos', name: 'app_about')]
    public function index(): Response
    {
        return $this->render('about/about.html.twig', [
        ]);
    }
}
