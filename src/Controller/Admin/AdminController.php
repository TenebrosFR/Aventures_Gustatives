<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\CreateRecipeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     */
    public function dashboard(Request $request, RecipeRepository $recipeRepository, UserRepository $userRepository, CategoryRepository $categoryRepository, CommentRepository $commentRepository, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
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

                // updates the 'imageFilename' property to store the PDF file name
                // instead of its contents
                $recipe->setDate(date_create('now'));
                $recipe->setImage($newFilename);
                $manager = $doctrine->getManager();
                $manager->persist($recipe);
                $manager->flush();
                return $this->redirectToRoute('dashboard');
            }
        }
        $RecipeList =   $recipeRepository->findAll();
        $UserList =     $userRepository->findAll();
        $CategoryList = $categoryRepository->findAll();
        $CommentList =  $commentRepository->findAll();
        return $this->render('admin/admin_dashboard.html.twig', [
            "form" => $form->createView(),
            'RecipeList' => $RecipeList,
            'UserList' => $UserList,
            'CategoryList' => $CategoryList,
            'CommentList' => $CommentList
        ]);
    }
}
