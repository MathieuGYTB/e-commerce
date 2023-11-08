<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Form\ContactType;

class ContactController extends AbstractController
{
    #[Route('/profile/contact', name: 'app_contact')]
    public function index(MailerInterface $mailer, Request $request): Response
    {
        //get user data
        $userName = ucfirst($this->getUser()->getName());
        $userFirstname = ucfirst($this->getUser()->getFirstname());
        $userEmail = $this->getUser()->getEmail();

        //get admin email
        $adminEmail = $_ENV["ADMIN_EMAIL"];

        $contactForm = $this->createForm(ContactType::class);

        $contactForm->handleRequest($request);

        if ($contactForm->isSubmitted() && $contactForm->isValid()) {

            $this->addFlash("send","Votre message a bien été envoyé.");
            $data = $contactForm->getData();
            $message = $data["message"];
            $email = (new TemplatedEmail())
            ->from($adminEmail)
            ->to($adminEmail)
            ->replyTo($userEmail)
            ->subject("Message provenant d'un utilisateur du site easywebjob")
            ->htmlTemplate("contact/email.html.twig")
            ->context([ 
                "message" => $message,
                "name" => $userName,
                "firstname" => $userFirstname,
                "userEmail" => $userEmail
            ]);
            $mailer->send($email);
        }

        return $this->render('contact/index.html.twig', [
            'contactform' => $contactForm->createView()
        ]);
    }
}
