<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request, \Swift_Mailer $mailer): Response
    {
        $form= $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $contact = $form->getData();

            $message = (new \Swift_Message('Nouveau contact'))
                ->setFrom($contact['email'])
                ->setTo('test@hotmail.fr')
                ->setBody( 
                    $this->renderView(
                        'emails/contact.html.twig', compact('contact')
                    ),
                    'text/html'
                )
                ;
                $mailer->send($message);
                $this->addFlash('message', 'Le message à bien été envoyé');
                return $this->redirectToRoute('confirmation');
        }
        return $this->render('contact/index.html.twig', [
            'contactForm' => $form->createView()
        ]);
    }

    /**
     * @Route("contact/confirmation.html.twig", name="confirmation")
     */
    public function confirmation(){
        return $this->render('contact/confirmation.html.twig');
    }
}
