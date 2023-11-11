<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\CartService;

class CartController extends AbstractController
{
    #[Route('/profile/cart', name: 'app_cart')]
    public function index(CartService $cartService): Response
    {

        return $this->render('cart/index.html.twig', [
            'items' => $cartService->getFullCart(),
            'total'=> $cartService->getTotal(),
            'currency' => 'euros'
        ]);
    }

    #[Route('/profile/cart/add/{id}', name:'app_cart_add')]
    public function add($id, CartService $cartService)
    {
        $cartService->add($id);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/profile/cart/remove/{id}', name:'app_cart_remove')]
    public function remove($id, CartService $cartService)
    {
        $cartService->remove($id);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/profile/cart/less/{id}', name:'app_cart_less')]
    public function less($id, CartService $cartService)
    {
        $cartService->less($id);
        return $this->redirectToRoute('app_cart');
    }
}
