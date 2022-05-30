<?php

namespace App\Controller\Recipe;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Entity\User;
use App\Form\CreateCommentType;
use App\Form\CreateRecipeType;
use App\Repository\CommentRepository;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Security;


/**
 * @Route("/recipes")
 */
class RecipesController extends AbstractController
{
    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    /**
     * @Route("/", name="recipes")
     */
    public function all(RecipeRepository $recipeRepository, UserRepository $userRepository): Response
    {
        $RecipeList =   $recipeRepository->findAll();
        return $this->render('recipe/recipes.html.twig', [
            'RecipeList' => $RecipeList,
        ]);
    }
    /**
     * @Route("/{id}", name="recipe_detail")
     */
    public function detail(int $id, RecipeRepository $recipeRepository, CommentRepository $commentRepository, Request $request, ManagerRegistry $doctrine): Response
    {
        $RecipeList =  array_slice(array_reverse($recipeRepository->findAll()),-3);
        $recipe = $recipeRepository->find($id);
        $comment = new Comment();
        $form = $this->createForm(CreateCommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            $comment
                ->setAuthor($user)
                ->setDate(date_create('now'))
                ->setRecipe($recipe);
            $manager = $doctrine->getManager();
            $manager->persist($comment);
            $manager->flush();
        }
        $comments = $commentRepository->findBy(array('recipe' => $recipe,'enabled' => true));

        return $this->render('recipe/recipe_detail.html.twig', [
            'Recipe' => $recipe,
            "form" => $form->createView(),
            "Comments" => $comments,
            "RecipeList" => $RecipeList,
        ]);
    }
}
