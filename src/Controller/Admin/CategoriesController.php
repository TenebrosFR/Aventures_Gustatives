<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CreateCategoryType;
use App\Form\DeleteCategoryType;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/admin")
 */
class CategoriesController extends AbstractController
{
    /**
     * @Route("/create_category", name="create_category")
     */
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        $category = new Category();
        $form = $this->createForm(CreateCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $doctrine->getManager();
            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('admin/category.html.twig', ["form" => $form->createView(),]);
    }
    /**
     * @Route("/delete_category", name="delete_category")
     */
    public function delete(Request $request, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(DeleteCategoryType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->remove($form["category"]->getData(),true);
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('admin/delCategory.html.twig', ["form" => $form->createView(),]);
    }
}
