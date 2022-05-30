<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\CreateRecipeType;
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


/**
 * @Route("/admin")
 */
class RecipesController extends AbstractController
{
    /**
     * @Route("/create_recipe", name="create_recipe")
     */
    public function create(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(CreateRecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $image */
            $image = $form->get('image')->getData();

            // this condition is needed because the 'image' field is not required
            // so the image file must be processed only when a file is uploaded
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                // Move the file to the directory where images are stored
                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $recipe->setDate(date_create('now'));
                // updates the 'imageFilename' property to store the PDF file name
                // instead of its contents
                $recipe->setImage($newFilename);
                $manager = $doctrine->getManager();
                $manager->persist($recipe);
                $manager->flush();
                return $this->redirectToRoute('dashboard');
            }
        }
        return $this->render('admin/recipes.html.twig', ["form" => $form->createView(),]);
    }
    /**
     * @Route("/update_recipe/{id}", name="update_recipe")
     */
    public function update(int $id, Request $request, RecipeRepository $recipeRepository, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $recipe = $recipeRepository->find($id);
        $form = $this->createForm(CreateRecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $image */
            $image = $form->get('image')->getData();

            // this condition is needed because the 'image' field is not required
            // so the image file must be processed only when a file is uploaded
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();
                $recipe->setImage($newFilename);
                // Move the file to the directory where images are stored
                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            }
            // updates the 'imageFilename' property to store the PDF file name
            // instead of its contents
            $recipe->setDate(date_create('now'));
            $manager = $doctrine->getManager();
            $manager->persist($recipe);
            $manager->flush();
            return $this->redirectToRoute('dashboard');
        }
        return $this->render('admin/updateRecipes.html.twig', ["form" => $form->createView(),]);
    }
    /**
     * @Route("/delete_recipe/{id}", name="delete_recipe")
     */
    public function delete(int $id, RecipeRepository $recipeRepository)
    {
        $recipeRepository->remove($recipeRepository->find($id), true);
        return $this->redirectToRoute('dashboard');
    }
}
