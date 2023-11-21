<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends AbstractController {

  #[Route(path:"/webhook", name:"app_webhook")]
  public function webhook(EntityManagerInterface $em, RequestStack $requestStack) : Response 
  {

    if($_ENV['APP_ENV'] === 'dev') {
      $stripeSecretKey = $_ENV['STRIPE_SECRET_KEY_DEV'];
      $endpoint_secret = $_ENV['STRIPE_SECRET_WHSEC_DEV'];
    } else if ($_ENV['APP_ENV'] === 'prod') {
      $stripeSecretKey = $_ENV['STRIPE_SECRET_KEY_PROD'];
      $endpoint_secret = $_ENV['STRIPE_SECRET_WHSEC_PROD'];
    }

    new \Stripe\StripeClient($stripeSecretKey);
    \Stripe\Stripe::setApiKey($stripeSecretKey);
    
    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    $event = null;
    $user = $this->getUser();

    try {
      $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
      );
    } catch(\UnexpectedValueException $e) {
      // Invalid payload
      http_response_code(400);
      exit();
    } catch(\Stripe\Exception\SignatureVerificationException $e) {
      // Invalid signature
      http_response_code(400);
      exit();
    }

    // Handle the event
    switch ($event->type) {
      case 'invoice.payment_succeeded':
        $invoice = $event->data->object;
        $pdf = $invoice->invoice_pdf;
        $user->setBill([$pdf]);
        $data = $invoice->lines->data;
        foreach ($data as $line) {
          $products[] = $line->description;
        }
        if(in_array('formation développeur web et mobile : les bases', $products)) {
          $user->setRoles(['ROLE_CUSTOMER_FORMATION']);
        } else {
          $user->setRoles(['ROLE_CUSTOMER']);
        }
        $em->persist($user);
        $em->flush();
        $cart = $requestStack->getSession()->get('cart');
        unset($cart);
        break;
      default:
        echo 'Received unknown event type ' . $event->type;
    }

    return new Response(http_response_code(200));
  }  
}
