<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BuyController extends AbstractController
{
    #[Route('/profile/buy', name: 'app_buy')]
    public function index(): Response
    {   

        $userEmail = $this->getUser()->getEmail();

        if ($_ENV['APP_ENV'] === 'dev') {
            $stripeSecretKey = $_ENV["STRIPE_SECRET_KEY_DEV"];
            $YOUR_DOMAIN = 'http://localhost:4242';
            $price = 'price_1OAUYUKgHxrl7uH3UAIoogET';
        } else if ($_ENV['APP_ENV'] === 'prod') {
            $stripeSecretKey = $_ENV['STRIPE_SECRET_KEY_PROD'];
            $YOUR_DOMAIN = 'https://easywebjob.fr';
            $price = 'price_1OAU3RKgHxrl7uH3XZW1s4tv';
        };
        
        \Stripe\Stripe::setApiKey($stripeSecretKey);
        header('Content-Type: application/json');

        $checkout_session = \Stripe\Checkout\Session::create([
            'billing_address_collection' => "required",
            'custom_text' => [
                'submit' => [
                    'message' => "En cliquant sur j'accepte, vous renoncez à votre droit à un délai de rétractation de 14 jours et ne pourrez demander un remboursement.",
                ],
            ],
            'consent_collection' => [
                'terms_of_service' => 'required',
            ],
            'customer_email' => $userEmail,
            'line_items' => [[
            # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                'price' => $price,
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'allow_promotion_codes' => true,
            'invoice_creation' => [
                'enabled' => true,
                'invoice_data' => [
                    'custom_fields' => [
                        [
                            'name' => 'SIRET',
                            'value' => '83218902100021',
                        ],
                        [
                            'name' => 'Code APE',
                            'value' => '6201Z',
                        ],
                        [
                            'name' => 'TVA',
                            'value' => 'non applicable ART.293B du CGI',
                        ],
                    ],
                ],
            ],
            'success_url' => $YOUR_DOMAIN . '/success',
            'cancel_url' => $YOUR_DOMAIN . '/cancel',
            'automatic_tax' => [
                'enabled' => true,
            ],
        ]);

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);

        return $this->redirect($checkout_session->url, 303);
    }

    #[Route(path:'/success', name:'app_success')]

    public function success(): Response
    {
        
        return $this->render('buy/success.html.twig');
    }

    #[Route(path:'/cancel', name:'app_cancel')]
    public function cancel(): Response
    {
        $this->addFlash('cancel', 'Votre achat a été annulé');

        return $this->redirectToRoute('app_home');

    }
}
