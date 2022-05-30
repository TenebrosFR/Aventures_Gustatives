<?php

namespace App\Controller\Home;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(RecipeRepository $recipeRepository): Response
    {
        $RecipeList =   array_reverse($recipeRepository->findAll());
        return $this->render('home/home.html.twig', [
            'RecipeList' => $RecipeList,
        ]);
    }
}
