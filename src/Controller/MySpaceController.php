<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MySpaceController extends AbstractController
{
    #[Route('/profile/my-space', name: 'app_my_space')]
    public function index(): Response
    {
        //get user datas
        $userName = ucfirst($this->getUser()->getName());
        $userFirstname = ucfirst($this->getUser()->getFirstname());
        $userEmail = $this->getUser()->getEmail();
        $userEmailVerifier = $this->getUser()->isVerified();
        $userInvoice = $this->getUser()->getBill();

        return $this->render('my_space/index.html.twig', [
            "name" => $userName,
            "firstname" => $userFirstname,
            "email" => $userEmail,
            "verified" => $userEmailVerifier,
            "invoices" => $userInvoice
        ]);
    }
}
