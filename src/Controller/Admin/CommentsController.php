<?php

namespace App\Controller\Admin;

use App\Repository\CommentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/admin")
*/
class CommentsController extends AbstractController
{
    #[Route('/comments', name: 'app_comments')]
    public function index(CommentRepository $commentRepository): Response
    {
        $CommentsList = $commentRepository->findAll();
        return $this->render('comments/comments.html.twig', [
            'CommentList' => $CommentsList,
        ]);
    }
    #[Route('/commentGestion{id}', name: 'commentGestion')]
    public function gestionComments(int $id,CommentRepository $commentRepository, ManagerRegistry $doctrine): Response
    {
        $Comment = $commentRepository->find($id);
        $Comment->setEnabled(!$Comment->isEnabled());

        $manager = $doctrine->getManager();
        $manager->persist($Comment);
        $manager->flush();
        return $this->redirectToRoute('app_comments');
    }
}
