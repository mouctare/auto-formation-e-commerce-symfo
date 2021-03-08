<?php

namespace App\Controller;

use App\Classes\Mail;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/nous-contacter", name="contact")
     */
    public function index(Request $request)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('notice', "Merci de nous avoir contacter. Notre équipe va vous répondre dans les meilleurs delais.");

           $content_contact = $form->getData();
            $content  = $content_contact['prenom'];
            $content .= '<br/>'. $content_contact['nom'];
            $content .= '<br/>'. $content_contact['email'];
            $content .= '<br/>'. $content_contact['content'];

             $email = new Mail();
            $email->send('mouctard78@gmail.com', 'La Boutique du nord', 'Vous avez reçu une nouvelle demande de conctate ', $content);

           
        }
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
