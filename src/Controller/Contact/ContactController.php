<?php

namespace App\Controller\Contact;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setDate(date_create('now'));
            $manager = $doctrine->getManager();
            $manager->persist($contact);
            $manager->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('contact/index.html.twig', ["form" => $form->createView(),]);
    }
    #[Route('/admin/contact', name: 'loadcontact')]
    public function loadContacted( ContactRepository $contactRepository): Response
    {
        $contactList = $contactRepository->findAll();

        return $this->render('contact/show.html.twig', ["ContactList" => $contactList]);
    }
}
